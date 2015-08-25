<?php 
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:GET,POST");
$url="http://www.zillow.com/webservice/GetDeepSearchResults.htm?zws-id=X1-ZWz1b30dldyivf_5w3uy&address=".($_GET["streetInput"])."&citystatezip=".$_GET["cityInput"]."%2C+".$_GET["stateInput"]."&rentzestimate=true";

    $xml=simplexml_load_file($url);
    $json=json_encode($xml);
    $obj=json_decode($json);
    $a= new StdClass;$a->chart= new StdClass;$a->rentchange= new StdClass;$a->allchange= new StdClass;
    
    $a->error=$obj->message->code;
    $a->homedetails=$obj->response->results->result->links->homedetails;
    $a->street=$obj->response->results->result->address->street;
    $a->zipcode=$obj->response->results->result->address->zipcode;
    $a->city=$obj->response->results->result->address->city;
    $a->state=$obj->response->results->result->address->state;

    $a->type=$obj->response->results->result->useCode;
    $a->yearbuilt=$obj->response->results->result->yearBuilt;
    $a->lotsize=($obj->response->results->result->lotSizeSqFt==null)?"N/A":number_format($obj->response->results->result->lotSizeSqFt,0)." sq. ft.";
    $a->area=($obj->response->results->result->finishedSqFt==null)?"N/A":number_format($obj->response->results->result->finishedSqFt,0)." sq. ft.";
    $a->bedrooms=$obj->response->results->result->bedrooms;
    $a->bathrooms=$obj->response->results->result->bathrooms;
    $a->tayear=$obj->response->results->result->taxAssessmentYear;

    function format($a)
    {
        if($a==null)
            return "N/A";
        else
            return "$".number_format($a,2);
    }
    $a->ta=format($obj->response->results->result->taxAssessment);    
    $a->lastsoldprice=format($obj->response->results->result->lastSoldPrice);
    $a->estamount=format($obj->response->results->result->zestimate->amount);
    $a->highprice=format($obj->response->results->result->zestimate->valuationRange->high);
    $a->lowprice=format($obj->response->results->result->zestimate->valuationRange->low);
    $a->rentamount=format($obj->response->results->result->rentzestimate->amount);
    $a->renthigh=format($obj->response->results->result->rentzestimate->valuationRange->high);
    $a->rentlow=format($obj->response->results->result->rentzestimate->valuationRange->low); 
    
    function newformat($a,$b)
    {
        if($a==null||(number_format($a,2)==null)){
            $b->amount="N/A";
            $b->sign=" ";
            $b->arw=" ";
        }
        else if($a>=0){
            $b->sign="+";
            $b->amount="$".number_format($a,2);
            $b->arw="<img src='http://cs-server.usc.edu:45678/hw/hw6/up_g.gif'>";
        }
        else if($a<0){
            $b->sign="-";
            $b->amount="$".number_format((-$a),2);
            $b->arw="<img src='http://cs-server.usc.edu:45678/hw/hw6/down_r.gif'>";
        }
    }
    newformat($obj->response->results->result->rentzestimate->valueChange,$a->rentchange);
    newformat($obj->response->results->result->zestimate->valueChange,$a->allchange);
    
    date_default_timezone_set("America/Los_Angeles");
    $date=date_create($obj->response->results->result->lastSoldDate);
    $date1=date_create($obj->response->results->result->zestimate->{'last-updated'});
    $date2=date_create($obj->response->results->result->rentzestimate->{'last-updated'});
    $a->datesold=(count($xml->response->results->result->lastSoldDate)==0)?null:date_format($date,"d-M-Y");
    $a->date1=(count($xml->response->results->result->zestimate->{'last-updated'})==0)?null:date_format($date1,"d-M-Y");
    $a->date2=(count($xml->response->results->result->rentzestimate->{'last-updated'})==0)?null:date_format($date2,"d-M-Y");

    $id=$obj->response->results->result->zpid;
    $xml1=simplexml_load_file("http://www.zillow.com/webservice/GetChart.htm?zws-id=X1-ZWz1b30dldyivf_5w3uy&unit-type=percent&chartDuration=1year&zpid=".$id."&unit-type:percent&width=600&height=300&");
    $a->chart->url1= (string)$xml1->response[0]->url[0];
    $xml5=simplexml_load_file("http://www.zillow.com/webservice/GetChart.htm?zws-id=X1-ZWz1b30dldyivf_5w3uy&unit-type=percent&chartDuration=5years&zpid=".$id."&unit-type:percent&width=600&height=300"); 
    $a->chart->url5= (string)$xml5->response[0]->url[0];
    $xml10=simplexml_load_file("http://www.zillow.com/webservice/GetChart.htm?zws-id=X1-ZWz1b30dldyivf_5w3uy&unit-type=percent&chartDuration=10years&zpid=".$id."&unit-type:percent&width=600&height=300"); 
    $a->chart->url10= (string)$xml10->response[0]->url[0];

    foreach($a as $k=>$v){
        if (($v==null)||($v=="$")||(($v=="$0.00")&&($k=="lastsoldprice"))||((($v=="01-Jan-1970")||($v=="31-Dec-1969"))&&(($k=="datesold")||($k=="date1")||($k=="date2")))){
            $a->$k="N/A";
        }
    }
$result=json_encode($a);
echo ($result);
?> 