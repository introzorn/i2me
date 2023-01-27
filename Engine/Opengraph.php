<?php

namespace App;

class Opengraph{
    private $Arr = [];
    public $type='website';
    public $title='';
    public $description="";
    public $image="";
    public $site_name="";
    public $video ="";
    public $locale="ru_RU";
    public $audio="";
    public $url="";

    public $OGHead='';



    public function AddOG($property,$content){
        $Arr[]=[$property,$content];
    }


    Public function GETMETA(){

        $farr=[];
        if($this->type!=''){$farr[]=['type',$this->type];}
        if($this->title!=''){$farr[]=['title',$this->title];}
        if($this->description!=''){$farr[]=['description',$this->description];}
        if($this->image!=''){$farr[]=['image',$this->image];}
        if($this->site_name!=''){$farr[]=['site_name',$this->site_name];}
        if($this->video!=''){$farr[]=['video',$this->video];}
        if($this->locale!=''){$farr[]=['locale',$this->locale];}
        if($this->audio!=''){$farr[]=['audio',$this->audio];}
        if($this->url!=''){$farr[]=['url',$this->url];}
        $A=array_merge($farr,$this->Arr);

        $this->OGHead='';

        for ($i=0; $i < sizeof($A) ; $i++) { 
           
            $this->OGHead.='<meta property="og:'.$A[$i][0].'" content= "'.$A[$i][1].'" />'."\r\n";

        }

        return $this->OGHead;
    }
}