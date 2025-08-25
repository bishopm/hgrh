<?php
 
namespace Bishopm\Hgrh\Livewire;

use Bishopm\Hgrh\Models\Document;
use Bishopm\Hgrh\Models\Tag;
use Livewire\Component;

class Search extends Component
{
    public $query;
    public $results;

    public function mount(){
        $this->query="";
        $this->results['documents']=Document::where('publish',1)->orderBy('document')->get();
    }

    public function tagsearch($slug){
        $tag=Tag::with('documents')->whereSlug($slug)->first();
        $this->query=$tag->name;
        $this->results['documents']=$tag->documents;
    }

    public function updatedQuery(){
        if (strlen($this->query) > 1){
            $this->results['documents']=Document::where('publish',1)->with('tags')->where('document','like','%' . $this->query . '%')
                ->orWhereHas('tags', function ($q) { $q->where('name', 'like', '%' . $this->query . '%'); })
                ->get();
        } else {
            $this->results['documents']=Document::where('publish',1)->orderBy('document')->get();
        }
    }

    public function render()
    {
        return view('hgrh::livewire.search');
    }
}