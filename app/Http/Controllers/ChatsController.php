<?php

namespace App\Http\Controllers;

use App\Models\chats;
use App\Http\Requests\StorechatsRequest;
use App\Http\Requests\UpdatechatsRequest;

class ChatsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorechatsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(chats $chats)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(chats $chats)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatechatsRequest $request, chats $chats)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(chats $chats)
    {
        //
    }
}
