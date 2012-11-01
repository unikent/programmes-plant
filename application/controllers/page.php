<?php
class Page_Controller extends Base_Controller
{

    public $restful = true;

    /**
     * This method checks to see if a view exists and if it does loads it along with any
     * associated data from the database. It then checks to see if a function exists for
     * the passed in page name (substituting dashes for underscores and runs that) if it exists.
     * @param  string  $page            The page name
     * @param  string  $variable        Allows us to pass a second variable to things
     * @return view                     Ideally a view of some kind
     */
    public function get_index($page = 'home',$variable=false)
    {        
        $this->data['page'] = Page::where('slug','=',$page)->first();
        $function = strtolower(Request::method().'_'.str_replace('-','_',$page));
        if(method_exists($this, $function)){
            $this->$function($variable);
        } else {
            if(View::exists('site.'.$page)){
                return View::make('site.'.$page,$this->data);
            }else{
                return Response::error('404');
            }            
        }  
    }
    public function post_index($page = 'home',$variable=false){
        $this->get_index($page,$variable);
    }

}