<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Resources\BooksResource;
use Illuminate\Http\Request;

class BooksController extends Controller
{

    public function index()
    {
        return BooksResource::collection(Book::with('ratings')->paginate(25));
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        Book::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return new BookResource($request);
    }

    public function show(Book $book)
    {
        return new BookResource($book);
    }

    public function update(Request $request, Book $book)
    {
        if ($request->user()->id !== $book->user_id) {
            return response()->json(['error' => 'You can only edit your own books'], 403);
        }

        $book->update($request->only(['title', 'description']));

        return new BookResource($book);
    }

    public function destroy(Book $book)
    {
        if ($request->user()->id !== $book->user_id) {
            return response()->json(['error' => 'You can only delete your own books'], 403);
        }

        $book->delete();

        return response()->json(null, 204);
    }
}
