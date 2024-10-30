<?php

abstract class codefairies_bookertools_ReturnType
{
    const Table = 0;
    const UnorderedList = 1;
}

function codefairies_bookertools_return_shows_ul($subtype,$subtypenaam,$show_limit){
	return codefairies_bookertools_return_shows_data($subtype,$subtypenaam,$show_limit,codefairies_bookertools_ReturnType::UnorderedList);
}
function codefairies_bookertools_return_shows_table($subtype,$subtypenaam,$show_limit){
	return codefairies_bookertools_return_shows_data($subtype,$subtypenaam,$show_limit,codefairies_bookertools_ReturnType::Table);
}

function codefairies_bookertools_return_shows_data($subtype,$subtypenaam,$show_limit,$returnType){
	$service = new codefairies_bookertools_service();
	$response = $service->codefairies_bookertools_GetFutureShow($subtype,$subtypenaam,$show_limit);
	$shortcode_options=get_option('bookertools-shortcode-option');

	$showtourname=false;
	$groupshowssamedate=false;
	$showticketlink=false;
	$showfacebooklink=false;
	if(isset($shortcode_options)){ 
		if(isset($shortcode_options['team_showtourname']) && $shortcode_options['team_showtourname']=='on'){
			$showtourname=true;
		}
	
		if(isset($shortcode_options['team_groupshowssamedate']) && $shortcode_options['team_groupshowssamedate']=='on'){
			$groupshowssamedate=true;
		}

		if(isset($shortcode_options['team_showticketlink']) && $shortcode_options['team_showticketlink']=='on'){
			$showticketlink=true;
		}

		if(isset($shortcode_options['team_showfacebooklink']) && $shortcode_options['team_showfacebooklink']=='on'){
			$showfacebooklink=true;
		}
	}
	
	$return_string = '';
	$return_string_ul='';
	$return_string_table='';

	$max=0;
	$teller=0;

	if(!is_null($response->data) && count($response->data)>0){
		if(is_numeric($show_limit)){
			$max=$show_limit;
		}else{
			$max=count($response->data);
		}
		//table start
		if($returnType==codefairies_bookertools_ReturnType::Table){
			$return_string_table.='<table class="bookertools-data">';
			$return_string_table.='<tr class="header">';
			$return_string_table.='<th scope="col" class="date">Date</th>';
			if($showtourname){
				$return_string_table.='<th scope="col" class="band">Band/Tour</th>';
			}else{
				$return_string_table.='<th scope="col" class="band">Band</th>';
			}
			$return_string_table.='<th scope="col" class="location">Location</th>';
			$return_string_table.='<th scope="col" class="city">City</th>';
			$return_string_table.='<th scope="col" class="country">Country</th>';
			if($showticketlink){
				$return_string_table.='<th scope="col" class="ticketlink"></th>';
			}
			if($showfacebooklink){
				$return_string_table.='<th scope="col" class="facebooklink"></th>';
			}

			$return_string_table.='</tr>';
		}

		//ul start
		if($returnType==codefairies_bookertools_ReturnType::UnorderedList){
			$return_string_ul.='<ul class="bookertools-data">';
		}
		
		$shows=$response->data;

		if($groupshowssamedate){
			//group shows on same date if on same date & location by :			
			//- adding bandname of band to previous record's band name if showtourname==false
			//- removing item from shows list if showtourname==true

			$newlist=array();
			$previousshow=null;
			
			foreach($shows as $show) { 
				if($newlist!=null){
					$previousshow=$newlist[count($newlist)-1];
					if($previousshow->date==$show->date 
					&& $previousshow->locationName==$show->locationName
					&& $previousshow->city==$show->city
					){
						$previousshow->bandName.=', '.$show->bandName;
					}else{
						array_push($newlist,$show);
					}
				}else{
					array_push($newlist,$show);
				}
			}
			
			$shows=$newlist;
		}
		
		foreach($shows as $show) { 
			//debug_to_console($show);
			if($teller<$max){
				//fill table
				if($returnType==codefairies_bookertools_ReturnType::Table){
					$return_string_table.= '<tr>';
					$return_string_table.= '<td class="date">'.codefairies_bookertools_parseIso8601date($show->date."").'</td>';
					if($showtourname && strlen($show->tourName)>0){
						$return_string_table.= '<td class="title">'.$show->tourName.'</td>';
					}else{
						$return_string_table.= '<td class="band">'.$show->bandName.'</td>';
					}
					if(strlen($show->locationUrl)>0){
						$return_string_table.= '<td class="location"><a href="'.$show->locationUrl.'" target="_blank">'.$show->locationName.'</a></td>';
					}else{
						$return_string_table.= '<td class="location">'.$show->locationName.'</td>';
					}
					$return_string_table.= '<td class="city">'.$show->city.'</td>';
					$return_string_table.= '<td class="country">'.$show->country.'</td>';
					if($showticketlink){
						if(isset($show->ticketLink) && strlen($show->ticketLink)>0){
							if($show->soldout){
								$return_string_table.= '<td class="ticketlink"><a href="'.$show->ticketLink.'" target="_blank">Sold out</a></td>';
							}else{
								$return_string_table.= '<td class="ticketlink"><a href="'.$show->ticketLink.'" target="_blank">Tickets</a></td>';
							}
						}else{
							if($show->soldout){
								$return_string_table.= '<td>Sold out</td>';
							}else{
								$return_string_table.= '<td></td>';
							}
						}
					}
					if($showfacebooklink){
						if(isset($show->facebookLink) && strlen($show->facebookLink)>0){
							$return_string_table.= '<td class="facebooklink"><a href="'.$show->facebookLink.'" target="_blank">Facebook event</a></td>';
						}	
					}
					$return_string_table.= '</tr>';
				}

				//fill ul
				if($returnType==codefairies_bookertools_ReturnType::UnorderedList){
					$return_string_ul.= '<li>';
					$return_string_ul.= '<span class="date">'.codefairies_bookertools_parseIso8601date($show->date."").' </span>';
					
					if($showtourname && strlen($show->tourName)>0){
						$return_string_ul.= '<span class="title">'.$show->tourName.'</span>';
					}else{
						$return_string_ul.= '<span class="band">'.$show->bandName.'</span>';
					}
					$return_string_ul.= '<span class="at"> at </span>';
					if(strlen($show->locationUrl)>0){
						$return_string_ul.= '<span class="location"><a href="'.$show->locationUrl.'" target="_blank">'.$show->locationName.'</a></span>';
					}else{
						$return_string_ul.= '<span class="location">'.$show->locationName.'</span>';
					}
					$return_string_ul.= '<span class="city">'.$show->city.'</span>';
					$return_string_ul.= '<span class="country"> ('.$show->country.')</span>';
					if($showticketlink){
						if(isset($show->ticketLink) && strlen($show->ticketLink)>0){
							$return_string_ul.= '<span class="ticketlink"><a href="'.$show->ticketLink.'" target="_blank">';
							if($show->soldout){
								$return_string_ul.='Sold out';
							}else{
								$return_string_ul.= 'Tickets';
							}
							$return_string_ul.='</a></span>';

						}else if($show->soldout){
							$return_string_ul.= '<span class="ticketlink">Sold out</span>';
						}
					}
					if($showfacebooklink){
						if(isset($show->facebookLink) && strlen($show->facebookLink)>0){
							$return_string_ul.= '<span class="facebooklink"><a href="'.$show->facebookLink.'" target="_blank">';
							$return_string_ul.= 'Facebook events';
							$return_string_ul.='</a></span>';
						}	
					}
					$return_string_ul.= '</li>';
				}
			}
			$teller++;
		}

		//table stop
		if($returnType==codefairies_bookertools_ReturnType::Table){
			$return_string_table.='</table>';
		}

		//ul stop
		if($returnType==codefairies_bookertools_ReturnType::UnorderedList){
			$return_string_ul.='</ul>';
		}

		if($returnType==codefairies_bookertools_ReturnType::UnorderedList)	$return_string = $return_string_ul;
		else if($returnType==codefairies_bookertools_ReturnType::Table) $return_string = $return_string_table;
	}
	else{
		$return_string = '<span class="bookertools-notfound">';
		if(isset($shortcode_options) && strlen($shortcode_options['team_noshowsfound'])>0){
			$return_string.=$shortcode_options['team_noshowsfound'];
		}else{
			$return_string.='No Shows found';
		}
		$return_string.='</p>';
	}

	return $return_string;
}

function codefairies_bookertools_return_tours_table($subtype,$subtypenaam,$limit){
	return codefairies_bookertools_return_tours_data($subtype,$subtypenaam,$limit,codefairies_bookertools_ReturnType::Table);
}

function codefairies_bookertools_return_tours_data($subtype,$subtypenaam,$limit,$returnType){
	$service = new codefairies_bookertools_service();
	$response = $service->codefairies_bookertools_GetFutureTours($subtype,$subtypenaam,$limit);
	$shortcode_options=get_option('bookertools-shortcode-option');
	
	$return_string = '';
	$return_string_ul='';
	$return_string_table='';

	$max=0;
	$teller=0;

	if(count($response->data)>0){
		if(is_numeric($limit)){
			$max=$limit;
		}else{
			$max=count($response->data);
		}
		//table start
		if($returnType==codefairies_bookertools_ReturnType::Table){
			$return_string_table.='<table class="bookertools-data">';
			$return_string_table.='<tr class="header">';
			$return_string_table.='<th scope="col" class="date">From</th>';
			$return_string_table.='<th scope="col" class="date">Until</th>';
			$return_string_table.='<th scope="col" class="title">Title</th>';
			/*$return_string_table.='<th scope="col" class="band">Band</th>';
			$return_string_table.='<th scope="col" class="otherbands">Other Bands</th>';*/
			$return_string_table.='</tr>';
		}

		//ul start
		if($returnType==codefairies_bookertools_ReturnType::UnorderedList){
			$return_string_ul.='<ul class="bookertools-data">';
		}

		foreach($response->data as $tour) { 
			//debug_to_console($tours);
			if($teller<$max){
				//fill table
				if($returnType==codefairies_bookertools_ReturnType::Table){
					$return_string_table.= '<tr>';
					$return_string_table.= '<td class="date">'.codefairies_bookertools_parseIso8601date($tour->fromDate."").'</td>';
					$return_string_table.= '<td class="date">'.codefairies_bookertools_parseIso8601date($tour->endDate."").'</td>';
					$return_string_table.= '<td class="title">'.$tour->tourName.'</td>';
					$return_string_table.= '</tr>';
					$return_string_table.= '<tr>';
					$return_string_table.= '<td>Bands : </td>';
					$return_string_table.= '<td class="band">'.$tour->bandName.'</td>';
					$return_string_table.= '<td class="otherbands">'.$tour->otherBands.'</td>';
					$return_string_table.= '</tr>';
				}

				//fill ul
				if($returnType==codefairies_bookertools_ReturnType::UnorderedList){
					$return_string_ul.= '<li>';
					$return_string_ul.= '<span class="date">'.codefairies_bookertools_parseIso8601date($tour->fromDate."").' </span>';
					$return_string_ul.= '<span class="date">'.codefairies_bookertools_parseIso8601date($tour->endDate."").' </span>';
					$return_string_ul.= '<span class="title">'.$tour->tourName.'</span>';
					$return_string_ul.= '</li>';
				}
			}
			$teller++;
		}

		//table stop
		if($returnType==codefairies_bookertools_ReturnType::Table){
			$return_string_table.='</table>';
		}

		//ul stop
		if($returnType==codefairies_bookertools_ReturnType::UnorderedList){
			$return_string_ul.='</ul>';
		}

		if($returnType==codefairies_bookertools_ReturnType::UnorderedList)	$return_string = $return_string_ul;
		else if($returnType==codefairies_bookertools_ReturnType::Table) $return_string = $return_string_table;
	}
	else{ 
	
		$return_string = '<span class="bookertools-notfound">';
		if(isset($shortcode_options) && strlen($shortcode_options['team_notoursfound'])>0){
			$return_string.=$shortcode_options['team_notoursfound'];
		}else{
			$return_string.='No Tours found';
		}
		$return_string.='</p>';
	}

	return $return_string;
}
?>