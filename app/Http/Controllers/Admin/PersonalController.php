<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmpleadoRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Gestión del personal del restaurante (rama 18). Solo para el admin.
 *
 * AISLAMIENTO, a diferencia de mesas y platos: el modelo User NO usa el trait
 * PerteneceARestaurante (el login necesita buscar usuarios sin filtro). Por eso
 * aquí se filtra a mano por el restaurante del admin:
 *  - en index(), con where('restaurante_id', ...);
 *  - cuando la URL trae un empleado (/admin/personal/{empleado}), lo busca el
 *    Route::bind('empleado') de routes/web.php, que también filtra: el empleado
 *    de otro restaurante da 404 antes de llegar a este controlador.
 *  - al crear, restaurante_id se pone aquí con el del admin; nunca sale del formulario.
 */
class PersonalController extends Controller
{
    public function index(Request $request): View
    {
        $empleados = User::delPersonal()->where('restaurante_id', $request->user()->restaurante_id)
            // Las cuentas de cocina antiguas no salen: la cocina ahora son dispositivos.
            ->whereIn('rol', EmpleadoRequest::ROLES)
            // withCount añade "pedidos_count" a cada empleado (una sola consulta
            // con COUNT): sirve para avisar al borrar a alguien con pedidos.
            ->withCount('pedidos')
            // Primero los admin y luego los camareros; dentro, por nombre.
            ->orderByRaw("FIELD(rol, 'admin', 'camarero')")
            ->orderBy('name')
            ->get();

        return view('admin.personal.index', ['empleados' => $empleados]);
    }

    public function create(): View
    {
        // Camarero por defecto: es el rol que más se crea.
        return view('admin.personal.formulario', ['empleado' => new User(['rol' => 'camarero'])]);
    }

    public function store(EmpleadoRequest $request): RedirectResponse
    {
        // safe()->only() solo trae lo validado. Un camarero no trae email ni
        // password (EmpleadoRequest los excluye) y un admin no trae pin.
        $empleado = new User($request->safe()->only(['name', 'email', 'rol', 'password', 'pin']));
        // La contraseña y el PIN se guardan cifrados solos, por los casts 'hashed' de User.

        // Estos dos no vienen del formulario:
        // - el restaurante es el del admin logueado;
        // - el correo de un admin se da por verificado, porque lo ha dado de
        //   alta el admin (así podría entrar aunque algún día se activara la verificación).
        $empleado->restaurante_id = $request->user()->restaurante_id;
        $empleado->email_verified_at = $empleado->rol === 'admin' ? now() : null;
        $empleado->save();

        $aviso = $empleado->rol === 'camarero'
            ? "«{$empleado->name}» ya puede entrar en los dispositivos de camarero con su PIN."
            : "«{$empleado->name}» ya puede iniciar sesión con {$empleado->email}.";

        return redirect()->route('admin.personal')->with('estado', $aviso);
    }

    public function edit(User $empleado): View
    {
        return view('admin.personal.formulario', ['empleado' => $empleado]);
    }

    public function update(EmpleadoRequest $request, User $empleado): RedirectResponse
    {
        $datos = $request->safe()->only(['name', 'email', 'rol']);
        $esCamarero = $datos['rol'] === 'camarero';

        // Lo que no toca a su rol se vacía: un camarero no tiene correo ni
        // contraseña y un admin no tiene PIN (por si se le cambia el rol).
        if ($esCamarero) {
            $datos += ['email' => null, 'password' => null, 'email_verified_at' => null];
        } else {
            $datos['pin'] = null;
        }

        // Contraseña (admin) o PIN (camarero) vacío = no se cambia.
        $campoClave = $esCamarero ? 'pin' : 'password';
        $cambiaClave = $request->filled($campoClave);
        if ($cambiaClave) {
            $datos[$campoClave] = $request->input($campoClave);
        }

        // forceFill: email_verified_at no está en Fillable.
        $empleado->forceFill($datos)->save();

        // Con la contraseña o el PIN nuevos, se cierran sus sesiones abiertas:
        // quien conociera el anterior deja de estar dentro. Si el admin se la
        // cambia a sí mismo, no se le echa de la sesión que está usando.
        if ($cambiaClave) {
            $this->cerrarSesiones($empleado, exceptoLaDe: $request);
        }

        return redirect()->route('admin.personal')
            ->with('estado', "Datos de «{$empleado->name}» guardados.");
    }

    public function destroy(Request $request, User $empleado): RedirectResponse
    {
        if ($empleado->is($request->user())) {
            return redirect()->route('admin.personal')
                ->with('aviso', 'No puedes eliminar tu propia cuenta.');
        }

        if ($empleado->rol === 'admin' && ! EmpleadoRequest::quedaOtroAdmin($empleado)) {
            return redirect()->route('admin.personal')
                ->with('aviso', 'El restaurante tiene que tener al menos un administrador.');
        }

        // Sus pedidos NO se borran: la clave foránea pedidos.user_id tiene
        // nullOnDelete, así que MySQL les pone user_id = NULL. Se conservan
        // las ventas y el historial; solo se pierde quién tomó la comanda.
        $this->cerrarSesiones($empleado);
        $empleado->delete();

        return redirect()->route('admin.personal')
            ->with('estado', "«{$empleado->name}» eliminado.");
    }

    /**
     * Borra las sesiones guardadas de un usuario (las sesiones están en la
     * tabla sessions de MySQL). La próxima vez que use la app, tendrá que
     * volver a iniciar sesión.
     */
    private function cerrarSesiones(User $empleado, ?Request $exceptoLaDe = null): void
    {
        DB::table('sessions')
            ->where('user_id', $empleado->id)
            ->when($exceptoLaDe, fn ($consulta) => $consulta->where('id', '!=', $exceptoLaDe->session()->getId()))
            ->delete();
    }
}
