<?php

namespace App\Http\Controllers;

use App\Projects\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{

    /**
     * Show all projects method
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::orderBy('id', 'DESC')->get();

        if (!$projects->isEmpty()) {
            return response(['projects' => $projects], 200);
        }

        return response(['message' => "There is no any projects in our database."], 404);

    }

    /**
     * Show project method
     * @param $id Project id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Project $id)
    {
        $project = Project::with('user')->find($id);

        if ($project) {
            return response(['project' => $project]);
        } else {
            return response(['message' => 'Project not found!'], 404);
        }
    }

    /**
     * Create new project method
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $validate = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()->all()]);
        }

        $project = Project::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'user_id' => $request->user()->id
        ]);

        return response(['project' => $project, 'projects_list' => Project::orderBy('id', 'DESC')->get()], 200);
    }

    /**
     * Update project method
     * @param Request $request
     * @param $id Project id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $data = json_decode($request->getContent(), true);

        $project = Project::find($id);

        if ($project) {

            if (\Gate::allows('isAdmin') || \Gate::allows('isOwner', $project)) {

                $validate = Validator::make($data, [
                    'name' => 'required|string|max:255',
                    'description' => 'required|string',
                    'user_id' => 'integer|exists:users,id',
                ]);

                if ($validate->fails()) {
                    return response(['errors' => $validate->errors()->all()], 422);
                }

                if (\Gate::denies('isAdmin')) {
                    unset($data['user_id']);
                }

                $update_project = $project->update($data);

                if ($update_project) {
                    $project = Project::find($id);

                    return response(['project' => $project], 200);

                } else {
                    return response(['message' => 'Project not updated something went wrong.'], 422);
                }

            } else {
                return response(['message' => 'You have no authorization for this action.'], 406);
            }

        } else {
            return response(['message' => "There is no projects with od [{$id}] in our database."], 404);
        }


    }


    public function destroy($id)
    {
        $project = Project::find($id);

        if ($project) {

            if (\Gate::allows('isAdmin') || \Gate::allows('isOwner', $project)) {

                if($project->delete()){
                    return response(['message' => 'Project successfully deleted.'], 200);
                }

                return response(['message' => 'Project not deleted something went wrong.'], 422);

            }else{
                return response(['message' => 'You have no authorization for this action.'], 406);
            }

        } else {
            return response(['message' => "There is no projects with od [{$id}] in our database."], 404);
        }


    }

}
