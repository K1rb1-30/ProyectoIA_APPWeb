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
        $empleados = User::where('restaurante_id', $request->user()->restaurante_id)
            // withCount añade "pedidos_count" a cada empleado (una sola consulta
            // con COUNT): sirve para avisar al borrar a alguien con pedidos.
            ->withCount('pedidos')
            // Primero los admin, luego camareros y cocina; dentro, por nombre.
            ->orderByRaw("FIELD(rol, 'admin', 'camarero', 'cocina')")
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
        // validated() solo trae name, email, rol y password: nada más del formulario.
        $empleado = new User($request->safe()->only(['name', 'email', 'rol', 'password']));
        // La contraseña se guarda con Hash sola, por el cast 'password' => 'hashed' de User.

        // Estos dos no vienen del formulario:
        // - el restaurante es el del admin logueado;
        // - el correo se da por verificado, porque lo ha dado de alta el admin
        //   (así podría entrar aunque algún día se activara la verificación).
        $empleado->restaurante_id = $request->user()->restaurante_id;
        $empleado->email_verified_at = now();
        $empleado->save();

        return redirect()->route('admin.personal')
            ->with('estado', "«{$empleado->name}» ya puede iniciar sesión con {$empleado->email}.");
    }

    public function edit(User $empleado): View
    {
        return view('admin.personal.formulario', ['empleado' => $empleado]);
    }

    public function update(EmpleadoRequest $request, User $empleado): RedirectResponse
    {
        $datos = $request->safe()->only(['name', 'email', 'rol']);

        // Contraseña vacía = no se cambia (no se toca la columna).
        $cambiaContrasena = $request->filled('password');
        if ($cambiaContrasena) {
            $datos['password'] = $request->input('password');
        }

        $empleado->update($datos);

        // Con la contraseña nueva, se cierran sus sesiones abiertas: quien
        // conociera la antigua deja de estar dentro. Si el admin se la cambia
        // a sí mismo, no se le echa de la sesión que está usando.
        if ($cambiaContrasena) {
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
