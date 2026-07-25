<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\Article;


class ServiceController extends Controller
{

public function resolvedTickets()
{
    $resolvedTickets = Ticket::where('status', 'Resolved')->get();
    return view('service.resolvedtickets', compact('resolvedTickets'));
}


public function knowledgeBase()
{
    $articles = Article::all(); // or filter by category/status if needed
    return view('service.knowledgebase', compact('articles'));
}



}
