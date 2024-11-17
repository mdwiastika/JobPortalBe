<?php

namespace App\Http\Controllers;

use App\Http\Resources\SkillResource;
use App\Models\Skill;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SkillController extends Controller
{
    public function index()
    {
        try {
            $skills = Skill::latest()->get();
            return new SkillResource('success', 'Skills retrieved successfully', $skills);
        } catch (\Throwable $th) {
            return new SkillResource('error', $th->getMessage(), null);
        }
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'skill_name' => 'required',
            ], [
                'skill_name.required' => 'The skill name field is required.',
            ]);
            $skill = Skill::create($validatedData);
            return new SkillResource('success', 'Skill created successfully', $skill);
        } catch (\Exception $th) {
            return new SkillResource('error', $th->getMessage(), null);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new SkillResource('error', $firstErrorMessages, null);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $skill = Skill::findOrFail($id);
            $validatedData = $request->validate([
                'skill_name' => 'required',
            ], [
                'skill_name.required' => 'The skill name field is required.',
            ]);
            $skill->update($validatedData);
            return new SkillResource('success', 'Skill updated successfully', $skill);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new SkillResource('error', $firstErrorMessages, null);
        } catch (ModelNotFoundException $th) {
            return new SkillResource('error', $th->getMessage(), null);
        } catch (\Throwable $th) {
            return new SkillResource('error', $th->getMessage(), null);
        }
    }
    public function destroy($id)
    {
        try {
            $skill = Skill::findOrFail($id);
            $skill->delete();
            return new SkillResource('success', 'Skill deleted successfully', null);
        } catch (ModelNotFoundException $th) {
            return new SkillResource('error', $th->getMessage(), null);
        } catch (\Throwable $th) {
            return new SkillResource('error', $th->getMessage(), null);
        }
    }
    public function jobPost()
    {
        return $this->index();
    }
}
