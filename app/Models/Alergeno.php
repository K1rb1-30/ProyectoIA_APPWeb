<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Alérgeno legal. NO usa PerteneceARestaurante: los 14 son comunes a todos
// los restaurantes y no tienen restaurante_id.
#[Fillable(['nombre'
