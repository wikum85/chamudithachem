function let_nonce(){
	return studentmgtSettings.nonce;
}

//Create User
function addNewStudent(user_ID,Data_Set){
	api_caller("POST",user_ID,Data_Set);
}
//Update User
function editStudent(user_ID,Data_Set){
	api_caller("PUT",user_ID,Data_Set);
}
//Disable User account
function ReactivateStudent(user_ID){
	pass = Math.floor((Math.random() * 100000) + 1);;
	Data_Set = {"status":"0","password":pass};
	api_caller("PUT",user_ID,Data_Set);
}

//Disable User account
function DeactivateStudent(user_ID){
	pass = Math.floor((Math.random() * 100000) + 1);;
	Data_Set = {"status":"1","password":pass};
	api_caller("PUT",user_ID,Data_Set);
}

//Payment Update
function monthlyPayment(user_ID,month,status){
	pass = Math.floor((Math.random() * 100000) + 1);;
	Data_Set = {"meta":{month:status}};
	api_caller("PUT",user_ID,Data_Set);
}


//Update/Create api call
function api_caller(Tyeope,user_ID,Data_Set){
	jQuery.ajax({
      type: Tyeope,
      url: "http://"+window.location.hostname+"/wp-json/wp/v2/users/"+user_ID,
      data: Data_Set,
      beforeSend: function(xhr) {
        xhr.setRequestHeader('X-WP-Nonce', let_nonce());
      },
    success: 
        function( data ) {
          console.log( data );
        }
    });
}