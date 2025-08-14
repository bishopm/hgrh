<?php
 
namespace Bishopm\Hgrh\Livewire;

use Bishopm\Hgrh\Models\Post;
use Bishopm\Hgrh\Models\Prayer;
use Bishopm\Hgrh\Models\Video;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Search extends Component
{
    public $query;
    public $results;

    public function mount(){
        $this->query="";
        $this->resetvalues();
    }

    public function resetvalues() {
        $this->results['blogs']=[];
        $this->results['videos']=[];
        $this->results['liturgies']=[];
    }

    public function updatedQuery(){
        if (strlen($this->query) > 1){
            $this->results['blogs']=Post::where('title','like','%' . $this->query . '%')->get();
            $this->results['videos']=Video::where('title','like','%' . $this->query . '%')->get();
            $this->results['liturgies']=Prayer::where('title','like','%' . $this->query . '%')->get();
        } else {
            $this->resetvalues();
        }
    }

    public function render()
    {
        return view('hgrh::livewire.search');
    }
}