<?php

namespace App\Controllers;

use App\Core\Controller;

class InfoController extends Controller
{
    public function carapinjam()
    {
        $this->view('info/carapinjam');
    }

    public function peraturan()
    {
        $this->view('info/peraturan');
    }
}
