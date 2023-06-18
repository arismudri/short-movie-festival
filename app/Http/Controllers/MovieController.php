<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddMovieRequest;
use App\Http\Requests\PaginationMovieRequest;
use App\Http\Requests\StreamMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\Storage;
use getID3;
use getid3_lib;

class MovieController extends Controller
{
    function getMostViewedGenre()
    {
        // $max = Movie::max('number_of_views');
        $genres = ['comedy', 'horror', 'romance'];
        $movies = collect([]);

        foreach ($genres as $genre) {
            $movies[$genre] = Movie::where('genre', $genre)->sum('number_of_views');
        }


        $maxValue = $movies->max();
        $most_viewed_genre = $movies->search($maxValue);

        return response()->json(compact("most_viewed_genre"), 200);
    }

    function getMostViewedMovie(PaginationMovieRequest $request)
    {
        $max = Movie::max('number_of_views');

        $movie = Movie::where('number_of_views',   $max)->paginate($request->per_page);

        return response()->json($movie, 200);
    }

    function getAll(PaginationMovieRequest $request)
    {
        if ($request->search) {
            $movie = Movie::where('title', 'LIKE', "%{$request->search}%")
                ->orWhere('description', 'LIKE', "%{$request->search}%")
                ->orWhere('artists', 'LIKE', "%{$request->search}%")
                ->orWhere('genre', 'LIKE', "%{$request->search}%")
                ->paginate($request->per_page);
        } else {
            $movie = Movie::paginate($request->per_page);
        }

        return response()->json($movie, 200);
    }

    public function stream(string $id)
    {
        $movie = Movie::findOrFail($id);
        $videoPath =  ($movie->file_path);

        if (!Storage::exists($videoPath)) {
            return response()->json([
                'message' => 'Movie tidak ditemukan',
            ], 404);
        }

        $stream = Storage::readStream($videoPath);

        $movie->number_of_views += 1;
        $movie->save();

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => 'video/mp4',
            'Accept-Ranges' => 'bytes',
            'Content-Length' => Storage::size($videoPath),
        ]);
    }

    public function store(AddMovieRequest $request)
    {
        $path = $request->file('file')->store('videos');

        $duration = $this->getVideoDuration(storage_path('app/' . $path));

        $movie = Movie::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $duration,
            'artists' => implode('|', $request->artists),
            'genre' => $request->genre,
            'file_path' => $path,
        ]);

        return response()->json($movie, 201);
    }

    public function update(UpdateMovieRequest $request)
    {
        $path = $request->file('file')->store('videos');

        $duration = $this->getVideoDuration(storage_path('app/' . $path));

        $movie = Movie::findOrFail($request->id);

        $movie->title = $request->title;
        $movie->description = $request->description;
        $movie->duration = $duration;
        $movie->artists = implode('|', $request->artists);
        $movie->genre = $request->genre;

        if (!empty($request->file('file')) && Storage::exists($movie->file_path)) {
            Storage::delete($movie->file_path);
            $movie->file_path = $path;
        }


        $movie->save();

        return response()->json($movie, 201);
    }

    private function getVideoDuration($videoPath)
    {
        $getID3 = new getID3;
        $fileInfo = $getID3->analyze($videoPath);

        if (isset($fileInfo['playtime_string'])) {
            $duration = $fileInfo['playtime_string'];
            return $duration;
        }

        return null;
    }
}
