<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlatoRequest;
use App\Models\Alergeno;
use App\Models\Categoria;
use App\Models\Plato;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Gestión de la carta del restaurante (solo admin).
 *
 * No hace falta filtrar por restaurante en ningún método: Plato y Categoria
 * usan el trait PerteneceARestaurante, así que todas las consultas ya vienen
 * filtradas por el restaurante del admin logueado. Además, si alguien pone en
 * la URL el id de un plato de otro restaurante, Laravel no lo encuentra y
 * devuelve un 404.
 */
class PlatoController extends Controller
{
    /**
     * Lista de platos, con filtro opcional por categoría (?categoria=3).
     */
    public function index(Request $request): View
    {
        $categorias = Categoria::withCount('platos')->orderBy('orden')->orderBy('nombre')->get();
        $categoriaElegida = $request->integer('categoria') ?: null;

        $platos = Plato::with(['categoria', 'alergenos'])
            // when() solo añade el where si se ha elegido una categoría.
            ->when($categoriaElegida, fn ($consulta) => $consulta->where('categoria_id', $categoriaElegida))
            ->orderBy('nombre')
            ->get();

        return view('admin.carta.index', [
            'platos' => $platos,
            'categorias' => $categorias,
            'categoriaElegida' => $categoriaElegida,
            'totalPlatos' => Plato::count(),
            'totalAgotados' => Plato::where('disponible', false)->count(),
        ]);
    }

    public function create(): View|RedirectResponse
    {
        // Sin categorías no se puede crear un plato: primero hay que hacer una.
        if (Categoria::doesntExist()) {
            return redirect()->route('admin.carta.categorias')
                ->with('aviso', 'Antes de añadir platos, crea al menos una categoría.');
        }

        return view('admin.carta.crear', $this->datosFormulario(new Plato(['disponible' => true])));
    }

    public function store(PlatoRequest $request): RedirectResponse
    {
        $plato = new Plato($this->datosValidados($request));

        if ($request->hasFile('foto')) {
            // Guarda el archivo en storage/app/public/platos con un nombre único.
            $plato->foto = $request->file('foto')->store('platos', 'public');
        }

        // restaurante_id lo pone el trait al crear.
        $plato->save();
        $plato->alergenos()->sync($request->input('alergenos', []));

        return redirect()->route('admin.carta')->with('estado', "Plato «{$plato->nombre}» añadido.");
    }

    public function edit(Plato $plato): View
    {
        return view('admin.carta.editar', $this->datosFormulario($plato));
    }

    public function update(PlatoRequest $request, Plato $plato): RedirectResponse
    {
        $plato->fill($this->datosValidados($request));

        // Foto nueva: borramos la anterior para no dejar archivos huérfanos.
        if ($request->hasFile('foto')) {
            $this->borrarFoto($plato);
            $plato->foto = $request->file('foto')->store('platos', 'public');
        } elseif ($request->boolean('quitar_foto')) {
            $this->borrarFoto($plato);
            $plato->foto = null;
        }

        $plato->save();
        // sync deja en la tabla pivote exactamente los alérgenos marcados.
        $plato->alergenos()->sync($request->input('alergenos', []));

        return redirect()->route('admin.carta')->with('estado', "Plato «{$plato->nombre}» guardado.");
    }

    public function destroy(Plato $plato): RedirectResponse
    {
        $this->borrarFoto($plato);
        // Las filas de alergeno_plato se borran solas (cascadeOnDelete).
        $plato->delete();

        return redirect()->route('admin.carta')->with('estado', "Plato «{$plato->nombre}» eliminado.");
    }

    /**
     * Interruptor de disponible / agotado de la lista.
     */
    public function cambiarDisponibilidad(Plato $plato): RedirectResponse
    {
        $plato->update(['disponible' => ! $plato->disponible]);

        $texto = $plato->disponible ? 'disponible' : 'agotado';

        return back()->with('estado', "«{$plato->nombre}» marcado como {$texto}.");
    }

    /**
     * Datos que necesita el formulario (crear y editar usan la misma vista).
     */
    private function datosFormulario(Plato $plato): array
    {
        return [
            'plato' => $plato,
            'categorias' => Categoria::orderBy('orden')->orderBy('nombre')->get(),
            'alergenos' => Alergeno::orderBy('id')->get(),
            // Ids de los alérgenos marcados. Si el formulario vuelve con
            // errores, old() recupera lo que se había marcado.
            'alergenosMarcados' => array_map('intval', old('alergenos', $plato->alergenos->pluck('id')->all())),
        ];
    }

    /**
     * Columnas del plato a partir del formulario ya validado.
     */
    private function datosValidados(PlatoRequest $request): array
    {
        return [
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'categoria_id' => $request->integer('categoria_id'),
            'precio_centimos' => $request->precioEnCentimos(),
            // Una casilla sin marcar no se envía: boolean() devuelve false.
            'disponible' => $request->boolean('disponible'),
        ];
    }

    private function borrarFoto(Plato $plato): void
    {
        if ($plato->foto) {
            Storage::disk('public')->delete($plato->foto);
        }
    }
}
