// Codes to show the dialogues
var query_string = {};
var query = window.location.search.substring(0);
var flag = '';
var flag_2 = '';
var flag_3 = '';
var flag_values = {};
var right_query = {};
if(query == ''){
	flag = 0;
}
else{
	right_query = query.split('=');
	flag_values = right_query[1].split('+');
	flag = flag_values[0];
	if(flag_values.length > 1){
		flag_2 = flag_values[1];
	}
	if(flag_values.length > 2){
		flag_3 = flag_values[2];
	}
}