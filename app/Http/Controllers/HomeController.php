<?php


namespace App\Http\Controllers;

class HomeController extends Controller {

    /**
     * Show the index page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Redirect to the explore page
        return redirect()->route('explore.index');
    }

}
