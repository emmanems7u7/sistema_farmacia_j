<?php

namespace App\Interfaces;

interface CorreoInterface
{
    public function EditarPlantillaCorreo($request, $email);

    public function EditarConfCorreo($email);

}
