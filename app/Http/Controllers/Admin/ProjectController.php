<?php

//Creates namespace for present controller
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//Uses Str class for slug creation
use Illuminate\Support\Str;
//Uses 'Project' model
use App\Models\Project;
//Uses 'Project' model
use App\Models\Type;
//Uses 'ProjectStoreRequest' request
use App\Http\Requests\ProjectStoreRequest;

use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller


{
    //'CREATE' FUNCTION
    public function create() {

        $types = Type::all();

        //Returns 'create' view
        return view('admin.projects.create', ['types' => $types]);
    }



    //'STORE' FUNCTION
    public function store(ProjectStoreRequest $request) {

        // DATA VALIDATION

        $data = $request->validated();

        // SLUG CREATING MECHANISM

        //Creating counter
        $counter = 0;

        do {

            //Creating slug variable from 'name', if counter > 0, counter is concatenated to slug
            $slug = Str::slug($data['name']) . ($counter > 0 ? '-' . $counter : '');

            //Searches for an entry with same slug as above
            $alreadyExists = Project::where('slug', $slug)->first();

            //Increments counter
            $counter++;

        //Iterates while an entry with same slug exists in database
        } while ($alreadyExists);

        //Assigns '$slug' value to 'slug' property of '$data' array
        $data['slug'] = $slug;

        //Saves 'image' file in 'storage' folder
        $image_path = Storage::put("project_images", $data["image"]);

        //Assigns '$image_path' value to 'image' property of '$data' array
        $data['image'] = $image_path;
        
        //Creates a new '$project' entry using '$data' validated from '$request' (see above)
        $project = Project::create($data);

        //Redirects to 'show' route with the id of newly created '$project' entry as second argument of 'route' function
        return redirect()->route('admin.projects.show', $project->slug);
    }



    //'INDEX' FUNCTION
    public function index() {

        //Fetches all entries in 'Project' table trough 'Project' model and saves in '$project' variable
        $projects = Project::all();

        //Returns 'index' view with 'projects' as second argument, from '$projects'
        return view('admin.projects.index', ['projects'=>$projects]);
    }



    //'SHOW' FUNCTION
    public function show($slug) {

        //Fetches an entry from 'Project' table trough 'Project' model using the '$slug' as finder for a 'where' query ('findOrFail()' works only with id's)
        $project = Project::where('slug', $slug)->firstOrFail();

        //Returns 'show' view with 'project' as second argument, from '$project'
        return view('admin.projects.show', ['project'=>$project]);
    }



    //'EDIT' FUNCTION
    public function edit($slug) {

        //Fetches an entry from 'Project' table trough 'Project' model using the '$slug' as finder for a 'where' query ('findOrFail()' works only with id's)
        $project = Project::where('slug', $slug)->first();

        $types = Type::all();

        //Returns 'show' view with 'project' as second argument, from '$project'
        return view('admin.projects.edit', ['project'=>$project], ['types'=>$types]);

    }



    //'UPDATE' FUNCTION
    public function update(ProjectStoreRequest $request, $slug) {

        // DATA VALIDATION

        $data = $request->validated();

        //Fetches an entry from 'Project' table trough 'Project' model using the '$slug' as finder for a 'where' query ('findOrFail()' works only with id's)
        $project = Project::where('slug', $slug)->first();

        // SLUG CREATING MECHANISM

        //Creating counter
        $counter = 0;

        do {

            //Creating slug variable from 'name', if counter > 0, counter is concatenated to slug
            $slug = Str::slug($data['name']) . ($counter > 0 ? '-' . $counter : '');

            //Searches for an entry with same slug as above
            $alreadyExists = Project::where('slug', $slug)->first();

            //Increments counter
            $counter++;

        //Iterates while an entry with same slug exists in database
        } while ($alreadyExists);

        //Saves '$slug' in '$data' array
        $data['slug'] = $slug;

        //Control to delete previous image from 'storage'
        if (key_exists("image", $data)) {

            //Saves 'image' file in 'storage' folder
            $image_path = Storage::put("project_images", $data["image"]);
        
            //Deletes previous image from 'storage'
            Storage::delete($project->image);
        }

        //Saves 'image' file in 'storage' folder
        $image_path = Storage::put("project_images", $data["image"]);

        //Assigns '$image_path' value to 'image' property of '$data' array
        $data['image'] = $image_path;

        //Updates '$project' using '$data'
        $project->update($data);

        //Redirects to 'show' route with the '$slug' of newly updated '$project' entry as second argument of 'route' function
        return redirect()->route('admin.projects.show', $project->slug);

    }



    public function destroy($slug) {

        //Fetches an entry from 'Project' table trough 'Project' model using the '$slug' as finder for a 'where' query ('findOrFail()' works only with id's)
        $project = Project::where('slug', $slug)->first();

        // Control to delete image from 'storage' folder
        if ($project->image) {

            Storage::delete($project->image);
        }

        //Deletes '$project'
        $project->delete();

        //Redirects to 'index' route
        return redirect()->route('admin.projects.index');

    }
}
