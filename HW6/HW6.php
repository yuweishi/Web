<head>
    <title>HW6</title>
    <style type="text/css">
        a {text-decoration:none}
        form {align:center;font-size:16px;}
        fieldset {margin-left:395px;margin-right:395px;padding-left:12px;padding-top:15px;border-color:#000000;border-style:solid;}
        div#container {width:1000px;height:1000px;font-size:15px}
        div#link {text-align:left;background-color:#F1EAC2;width:900px;height:25px;border-style:solid;border-radius:5px;border-width:1px;border-color:#000000;margin-top:20px}
        div#part1 {background-color:#ffffff; height:250px; width:257px; line-height:26px; text-align:left; float:left;margin-left:53px}
        div#part2 {background-color:#ffffff; height:250px; width:130px; line-height:26px;text-align:left; float:left;}
        div#part3 {background-color:#ffffff; height:250px; width:320px; line-height:26px;text-align:left; float:left;}
        div#part4 {background-color:#ffffff; height:250px; width:187px; line-height:26px;text-align:right; float:left;}
        div#footer {clear: both}
        sup {font-size:75%; line-height:0; position:relative; vertical-align:baseline; top:-0.5em;}
    </style>
</head>
<br><br>        
<h2 style="text-align:center">Real Estate Search</h2>
<form name="myform" method="POST" id="info">
    <fieldset>
        Street Address*: <input type="text" name="address" value=""><br>
        City*:<input type="text" name="city" value="" style="margin-left:70px;margin-top:4px;"><br>
        State*:<select name="state" size="1" value="" style="margin-left:66px;margin-top:4px;">
            <option value="noselection"></option>
            <option value="AK">AK</option>
            <option value="AL">AL</option>
            <option value="AR">AR</option>
            <option value="AZ">AZ</option>
            <option value="CA">CA</option>
            <option value="CO">CO</option>
            <option value="CT">CT</option>
            <option value="DC">DC</option>
            <option value="DE">DE</option>
            <option value="FL">FL</option>
            <option value="GA">GA</option>
            <option value="HI">HI</option>
            <option value="IA">IA</option>
            <option value="ID">ID</option>
            <option value="IL">IL</option>
            <option value="IN">IN</option>
            <option value="KS">KS</option>
            <option value="KY">KY</option>
            <option value="LA">LA</option>
            <option value="MA">MA</option>
            <option value="MD">MD</option>
            <option value="ME">ME</option>
            <option value="MI">MI</option>
            <option value="MN">MN</option>
            <option value="MO">MO</option>
            <option value="MS">MS</option>
            <option value="MT">MT</option>
            <option value="NC">NC</option>
            <option value="ND">ND</option>
            <option value="NE">NE</option>
            <option value="NH">NH</option>
            <option value="NJ">NJ</option>
            <option value="NM">NM</option>
            <option value="NV">NV</option>
            <option value="NY">NY</option>
            <option value="OH">OH</option>
            <option value="OK">OK</option>
            <option value="OR">OR</option>
            <option value="PA">PA</option>
            <option value="RI">RI</option>
            <option value="SC">SC</option>
            <option value="SD">SD</option>
            <option value="TN">TN</option>
            <option value="TX">TX</option>
            <option value="UT">UT</option>
            <option value="VA">VA</option>
            <option value="VT">VT</option>
            <option value="WA">WA</option>
            <option value="WI">WI</option>
            <option value="WV">WV</option>
            <option value="WY">WY</option>
        </select><br>
        <input type="submit" name="submit" value="search" style="margin-left:109px;margin-top:4px;"/>
        <img src="http://www.zillow.com/widgets/GetVersionedResource.htm?path=/static/logos/Zillowlogo_150x40_rounded.gif" width="150" height="40" alt="Zillow Real Estate Search" align="top"/>
        <p style="font-style:italic;margin:0;margin-top:10px;">* -Mandatory fields.</p>
    </fieldset>
</form>
<?php
$url="http://www.zillow.com/webservice/GetDeepSearchResults.htm?zws-id=X1-ZWz1b30dldyivf_5w3uy&address=";
$invalid="Please enter value for ";
$count=0;
if(isset($_POST["submit"])){ 
    foreach($_POST as $k=>$v){
        switch ($k) {
        case "address":
            if(!empty($v))
                $url.=str_replace(" ","+","$v")."&citystatezip=";
            else{
                $invalid.="Street Address";
                $count++;
            }
            break;
        case "city":
            if(!empty($v))
                $url.=str_replace(" ","+","$v")."%2C+";
            else{
                if($count==1)
                    $invalid.=" and City";
                else
                    $invalid.="City";
                $count++;
            }
            break;
        case "state":
            if($v!="noselection")
                $url.=$v."&rentzestimate=true";
            else{
                if($count==0)
                    $invalid.="State";
                elseif($count==1)
                    $invalid.=" and State";
                else
                    $invalid="Please enter value for Street Address, City and State";
                $count++;
            }
            break;
        case "submit":
            $invalid.=".";
        }
    }
    if($count>0)
        echo "<script type='text/javascript'>alert('$invalid');</script>";
    else{
        $xml=simplexml_load_file($url);
        if($xml->message->code == 508){
            echo "<center><b style='font-size:19.5px;'>No exact match found--Verify that the given address is correct.</b></center>";
        }
        else{
            echo "<center><h2 style='margin:0;margin-top:10px;'>Search Results</h2></center>";
            parserxml($xml);
        }
    }
}
function parserxml($xml){
    $link=$xml->response->results->result->links->homedetails;
    $street=$xml->response->results->result->address->street;
    $zipcode=$xml->response->results->result->address->zipcode;
    $city=$xml->response->results->result->address->city;
    $state=$xml->response->results->result->address->state;
    $arr1=array(
        "Property Type"=>$xml->response->results->result->useCode,
        "Year Built"=>$xml->response->results->result->yearBuilt,
        "Lot Size"=>$xml->response->results->result->lotSizeSqFt,
        "Finished Area"=>$xml->response->results->result->finishedSqFt,
        "Bathrooms"=>$xml->response->results->result->bathrooms,
        "Bedrooms"=>$xml->response->results->result->bedrooms,
        "Tax Assessment Year"=>$xml->response->results->result->taxAssessmentYear,
        "Tax Assessment"=>$xml->response->results->result->taxAssessment);
    date_default_timezone_set("America/Los_Angeles");
    $date1=date_create($xml->response->results->result->zestimate->{'last-updated'});
    $date2=date_create($xml->response->results->result->rentzestimate->{'last-updated'});
    $range=array($xml->response->results->result->zestimate->valuationRange->low,$xml->response->results->result->zestimate->valuationRange->high);
    $rentrange=array($xml->response->results->result->rentzestimate->valuationRange->low,$xml->response->results->result->rentzestimate->valuationRange->high);
    if(empty($xml->response->results->result->zestimate->valuationRange->low) || empty($xml->response->results->result->zestimate->valuationRange->high)) $range=null;
    if(empty($xml->response->results->result->rentzestimate->valuationRange->low) || empty($xml->response->results->result->rentzestimate->valuationRange->high)) $rentrange=null;
    $arr2=array(
        "Last Sold Price"=>$xml->response->results->result->lastSoldPrice,
        "Last Sold Date"=>date_create($xml->response->results->result->lastSoldDate),
        "Zestimate <sup>&reg</sup> Property Estimate as of "=>$xml->response->results->result->zestimate->amount,
        "30 Days Overall Change"=>$xml->response->results->result->zestimate->valueChange,
        "All Time Property Range"=>$range,
        "Rent Zestimate <sup>&reg</sup> Valuation as of "=>$xml->response->results->result->rentzestimate->amount,
        "30 Days Rent Change"=>$xml->response->results->result->rentzestimate->valueChange,
        "All Time Rent Range"=>$rentrange);
    if (count($xml->response->results->result->lastSoldDate)==0)
        $arr2["Last Sold Date"]=null;
    if (count($xml->response->results->result->zestimate->valueChange)==0||empty($xml->response->results->result->zestimate->valueChange))
        $arr2["30 Days Overall Change"]=null;
    if (count($xml->response->results->result->rentzestimate->valueChange)==0||empty($xml->response->results->result->rentzestimate->valueChange))
        $arr2["30 Days Rent Change"]=null;
    if (empty($xml->response->results->result->zestimate->amount))
        $arr2["Zestimate <sup>&reg</sup> Property Estimate as of "]=null;
    echo "<center><div id='container'><div id='link'>See more details for <a href='$link'><b>$street, $city, $state-$zipcode</b></a> on Zillow</div><div id='part1'>";
    foreach($arr1 as $k=>$v)
        echo $k.":<br>";
    echo "</div><div id='part2'>";
    foreach($arr1 as $k=>$v){
        if (count($v)==0)
            echo "<br>";
        else
        switch ($k){
            case "Lot Size":
                echo $v." sq. ft.<br>";break;
            case "Finished Area":
                echo $v." sq. ft.<br>";break;
            case "Tax Assessment":
                echo "$".number_format("$v",2);break;
            default:
            echo $v."<br>";
        }
    }
    echo "</div><div id='part3'>";
    foreach($arr2 as $k=>$v){
        if ($k=="Zestimate <sup>&reg</sup> Property Estimate as of ")
            echo $k.date_format($date1,"d-M-Y").":<br>";
        elseif($k=="Rent Zestimate <sup>&reg</sup> Valuation as of ")
            echo $k.date_format($date2,"d-M-Y").":<br>";
        elseif($k=="30 Days Overall Change"||$k=="30 Days Rent Change"){
            if ($v>0)
                echo $k."<img src='http://cs-server.usc.edu:45678/hw/hw6/up_g.gif'>:<br>";
            elseif($v<0)
                echo $k."<img src='http://cs-server.usc.edu:45678/hw/hw6/down_r.gif'>:<br>";
            else
                echo $k.":<br>";
        }
        else
            echo $k.":<br>";
    }
    echo "</div><div id='part4'>";
    foreach($arr2 as $k=>$v){
        if (count($v)==0)
            echo "<br>";
        else
        switch ($k){
            case "Last Sold Price":
                echo "$".$v."<br>";break;
            case "All Time Property Range":
                echo "$".number_format("$v[0]",2)." - "."$".number_format("$v[1]",2)."<br>";break;
            case "All Time Rent Range":
                echo "$".number_format("$v[0]",2)." - "."$".number_format("$v[1]",2)."<br>";break;
            case "Last Sold Date":
                echo date_format($v,"d-M-Y")."<br>";
                break;
            default:
            $v=abs($v);
            echo "$".number_format("$v",2)."<br>";
        }
    }
    echo "</div><div id='footer'><p>&copy; Zillow, Inc., 2006-2014. Use is subject to <a href='/corp/Terms.htm'>Terms of Use</a><br/><a href='/wikipages/What-is-a-Zestimate/'>What's a Zestimate?</a></p></div></div></center>";
}
?>