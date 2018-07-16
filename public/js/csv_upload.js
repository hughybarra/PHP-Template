/* js stored in js/csv_uplaod.js */

(function($) {
	var app = {
		// local init vars 
		app_state: 'widget_home',// defaults to widget home

		// home
		file_input: $('#csv_file_input'),
		card_widget_container: $('.card_widget_container'),
		home_drop_zone: $('#drop_zone'),

		// error vars
		server_error_message: $('#server_error_message'),
		error_message: 'Soemthing Went Wrong Please Try Again Later',
		error_event: '',


		// matching vars
		link_array: [],
		server_file_path: '',

		/* 
		* init 
		* initializes the app 
		*/
		init: function(){
			// set the default stage to wiget home
			this.state_handler('widget_home');
			// call the home handler
			this.home_handler();
		}, 

		/*
		* zero_out
		* zeros out the widget 
		*/
		zero_out: function(ev){
			// check for error_event 
			if(this.error_event){
				// set the event as the error event
				ev = this.error_event;
				// clearout the error_event
				this.error_event = '';
			}
			this.server_file_path = '';
			this.link_array = [];
			this.error_message = 'Soemthing Went Wrong Please Try Again Later';
			this.app_state = 'widget_home';
			// zero out the server file path 
			this.server_file_path = '';
			// remove children from zero out
			$('#fl_fields_container').empty();
			// remove children from error container 
			$('#error_data').empty();


			// zero out the input select
			this.file_input.replaceWith(this.file_input.val('').clone(true));

			// check if we passed an event 
			if(ev.type === 'change'){
				// zero out the input select
				this.file_input.replaceWith(this.file_input.val('').clone(true));
			}else{
				if(ev.type != 'click'){
					// zero out the data transfer items
					if (ev.dataTransfer.items) {
					    // Use DataTransferItemList interface to remove the drag data
					    ev.dataTransfer.items.clear();
					} else {
					    // Use DataTransfer interface to remove the drag data
					    ev.dataTransfer.clearData();
					}						
				}
	
			}

			this.state_handler('widget_home');
		},// end zero_out

		/*
		* stateHandler 
		* shows or hides the state
		*/
		state_handler: function(state){

			// hide all he states
			$('#widget_loading').hide();
			$('#widget_home').hide();
			$('#widget_success').hide();
			$('#widget_matching').hide();
			$('#widget_error').hide();

			switch (state) {
			    case 'widget_home':
			        $('#widget_home').show();
			        break;
			    case 'widget_loading':
			        $('#widget_loading').show();
			        break;
			    case 'widget_success':
			        $('#widget_success').show();
			        break;
			    case 'widget_matching': 
			    	$('#widget_matching').show();
			    	break;
			    case 'widget_error': 
			    	$('#widget_error').show();
			    	break;
			}// end switch 

		},// end state_handler 

		/*
		* home_drop_handler
		* @param event
		* when the user drops a file on the drophandler it will process that file 
		* if the file is not a file it will display an error
		* if the file is not a csv it will display an error
		*/
		home_drop_handler: function(event){
			// removethe background
			this.home_drop_zone.removeClass('text-white bg-success');

			// set state to loading 
			this.state_handler('widget_loading');

			// Prevent default behavior (Prevent file from being opened)
			event.preventDefault();
			// create a array to store multiple files 
			var filesArray = [];
			// check if dropped files has items 
			if (event.dataTransfer.items) {
			    // Use DataTransferItemList interface to access the file(s)
			    for (var i = 0; i < event.dataTransfer.items.length; i++) {
			        // If dropped items aren't files reject them
			        if (event.dataTransfer.items[i].kind === 'file') {
			        	// grab the file 
			        	file = event.dataTransfer.items[i].getAsFile();
			        	//check extension
			        	var ext = file.name.split('.').pop();
			        	// check that flie is a csv
			        	if(ext === 'csv'){
			        		// we got csvs
				            // push all valid files into the files Array 
				            filesArray.push(file);
			        	}else{
			        		//file type invalid 
			        		this.set_error('file Input is invalid', event);
			        		return;
			        	}
			        }else{
			        	//file type invalid
			        	this.set_error('file input is invalid', event);
			        	return;
			        }
			    }// end for 
				// call upload_csv
				this.upload_csv(filesArray, event);
			}else{
		    	//Something weng wrong
		    	this.set_error('Soemthing went wrong', event);
			}// end else
		}, // end home_drop_handler

		/*
		* home_dragover_handler
		* @param event
		* prevents the default behavior
		* addss clss to container 
		*/
		home_dragover_handler:function(event){
			// add class to container 
			this.home_drop_zone.addClass('text-white bg-success');
			// Prevent default behavior (Prevent file from being opened)
			event.preventDefault();
		},// end home_dragover_handler

		/*
		* home_dragleave_handler
		* @param event
		* removes class from container 
		* prevents default behavior
		*/
		home_dragleave_handler: function(event){
			this.home_drop_zone.removeClass('text-white bg-success');
			event.preventDefault();
		}, // end home_dragleave_handler

		/*
		* home_handler
		*/
		home_handler: function(){
			var self = this;
			/*
			* Input field 
			* @param file 
			* input field 
			* register change event on the handler 
			*/
			this.file_input.on({
				'change': function(event){
					self.state_handler('widget_loading');
					var filesArray = [];
					// loop over files 
					for(var x = 0; x < event.target.files.length; x ++){
						var file = event.target.files[x];
						// check that file type is correct
						if(file.type === 'text/csv'){
							filesArray.push(file);
						}else{
							this.set_error('file input is invalid', event);
							break;
						}
					}
					self.upload_csv(filesArray, event);
				},// end change
			});// end file_input register event
		},// end home handler 

		/*
		* set_error
		* @param errorMessage STring
		* @param event 
		* @param data array of strings
		* sets the error for the error message
		*/
		set_error: function(errorMessage, event, data){
			// set local vaible event for futre zero out
			this.error_event = event;
			// check for error message 
			if(!errorMessage){
				errorMessage = this.error_message;
			}

			// check if we have additional data 
			if(data){

				//loop over the strings in the array 
				for(var x =0; x < data.length; x ++){

					var warning = '<p><span class="text-danger">'+data[x]+'</span> Had empty values. Please fix your CSV and re-upload you file. Thank you.</p>'

					// append the errors to the error secreen 
					$('#error_data').append(warning);
				}
			}
			// clear out the error message
			this.server_error_message.text(errorMessage);
			this.state_handler('widget_error');
		},// end set_error


		/*
		* build_user_field_li
		* @param String fieldname
		* builds a user field and returns the li 
		*/
		build_user_field_li: function(field_name){
			var newLi = '<li id="'+field_name+'" class="list-group-item" draggable="true" ondragstart="matching_field_drag(event)">'+ field_name+'</li>';
			return newLi;
		},// end build_user_field_li

		/*
		* build_fl_fields
		* @param String fieldname
		* builds a flfield and returns the complex strings
		*/
		build_fl_fields: function(field_name){

			var inject_li = 
			'<li id="'+field_name+'" class="list-group-item">'+
	        	'<div class="row">'+
	        		'<div class="list-group-left col-sm-6">'+
	        			'<div class="form-group">'+
	        				'<div id="dropzone_'+field_name+'" field_name="'+field_name+'" class="user_field_drop_zone" ondragover="matching_dragover(event)" ondrop="matching_drop(event)">Drop Here</div>'+
	        				'<!-- arrows -->'+
	        				'<div class="arrows_group float-right">'+
		        				'<span class="oi oi-arrow-thick-left"></span>'+
		        				'<span class="oi oi-arrow-thick-right"></span>'+
	        				'</div>'+
	        			'</div>'+
	        		'</div><!--end list group left -->'+
	        		'<div class="list-group-right col-sm-6">'+
	        			'<p class="float-left">'+field_name+'</p>'+

	        			'<div class="close_button_container float-right">'+
	        				'<span id=remove_"'+field_name+'" remove_for="'+field_name+'" class="oi oi-circle-x" onclick="remove_link(event)"></span>'+
	        			'</div>'+
	        		'</div><!-- end list group right -->'+
	        	'</div>'+
	    	'</li>';
	    	return inject_li;

		},//end build_fl_fields

		/*
		* remove_link 
		* @param event 
		* removes link from the array 
		*/
		remove_link: function(event){
			var match_id = event.target.getAttribute('remove_for')
			// loop over the link array 
			for(var x = 0; x < this.link_array.length; x ++){
				// look for matching id
				if(this.link_array[x].fl_field === match_id){
					// remove match from text field 
					$('#dropzone_'+match_id).text('Drop Here');
					// remove link from array 
					this.link_array.splice(x,1);
					break;
				}
			}
		},// end remove_link 

		/*
		* create_link 
		* @param fl_field
		* @param user_field
		* matches a fl_field to a user_field in the associative array 
		*/
		create_link: function(fl_field, user_field){
			// create new object and assign vals
			var obj = {};
			obj.fl_field = fl_field;
			obj.user_field = user_field;
			// push our new link object into the links array 
			this.link_array.push(obj);	
		},// end create_link 

		/*
		* matching_field_drag
		* @param event
		* sets the data transfer data for the drag event
		*/
		matching_field_drag: function(event){
			event.dataTransfer.setData("text", event.target.id);
		},// end matching_field_drag


		/*
		* matching_drop
		* @param event 
		* handles the dropped element 
		*/
		matching_drop: function(event){
			// prevent default functionality 
		    event.preventDefault();
		    var data = event.dataTransfer.getData("text");
		    //dynamically set html of continer 
		    $('#'+event.target.id).text(data);

		    this.create_link(event.target.getAttribute('field_name'), data);
		   
		},// end matching_drop

		/*
		* matching_dragover
		* @param event
		* dynamic matching drop handler
		*/
		matching_dragover: function(event){
			// prevent default functionality
			event.preventDefault();
		},// end matching drop 


		/*
		* submit_linked_fields
		* @param event
		* submits the linked fields to the server 
		*/
		submit_linked_fields: function(event){
			var self = this;

			// check for data in link_array and sever_file path 
			if(!this.link_array.length){
				alert('Need to link at least one field');
				return;
			}

			if(this.link_array && this.server_file_path){
				this.state_handler('widget_loading');
				// create our post data
				var post_data = {};
				post_data.linked_fields = this.link_array;
				post_data.server_file_path = this.server_file_path;
				console.log('file name');
				console.log(this.server_file_path);
				// url path 
				var url = 'csv_handler.php';
			    // post the data to the server
				$.ajax({
				    url: url,
			       	dataType: 'json',  // what to expect back from the PHP script, if anything
			       	type: "POST",
			        data: post_data,                         
			        // type: 'post',
				    success: function(resp){
				    	// set event for future zeroout
				    	self.error_event = event;
				    	self.state_handler('widget_success');
				    },
				    error: function(err, data) {
				    	// check for json response
				    	if(err.responseJSON){
				    		// check fordata in json
				    		if(err.responseJSON.data){
				    			self.set_error(err.responseJSON.message, event, err.responseJSON.data);
				    		}
				    		self.set_error(err.responseJSON.message, event);
				    	}else{
				    		self.set_error('Server error please try again later', event);
				    	}

				    }
				});// end ajax 
			}
		},//end submit_linked_fields

		/*
		* init_widget_matching
		* @param object
		* initialize the field matching section 
 		*/
		init_widget_matching: function(data){
	    	// set the stateto widget matching
	    	this.state_handler('widget_matching');

			// loop over usr fields 
			for(var x = 0; x < data.user_fields.length; x ++){
				$('#user_fields_container').append(this.build_user_field_li(data.user_fields[x]));
			}


			// // loop over fl fields 
			for(var y = 0; y < data.fl_fields.length; y ++){
				var obj = {};
				obj.fl_field = data.fl_fields[y];
				obj.user_field = undefined;
				// push item into array 
				// this.link_array.push(obj);

				// create li element and append
				$('#fl_fields_container').append(this.build_fl_fields(data.fl_fields[y]));
			}
			// add the submitbutton at the bottom 
			$('#fl_fields_container').append('<div class="btn btn btn-success" onclick="submit_linked_fields(event)">Submit Links</div>');


		},// end init_widget_matching

		/*
		* upload_csv
		* @param csv file 
		* attempts to upload the provided csv/csv's to the server
		*/
		upload_csv: function(fileArray, event){
			var self = this;
			// check for form data availability 
			if(window.FormData === undefined){
				// form data is not available so users wont be able to 
				this.set_error('You need to use a different browser in order to uplaod a csv', ev);
			}else{
				// form data is available so we can upload
				// var url = '/?action=upload';
				var url = 'csv_handler.php';
			    var form_data = new FormData();    
			    form_data.append('csv_file', fileArray[0]);
			    // post the data to the server
				$.ajax({
				    url: url,
			       	// dataType: 'json',  // what to expect back from the PHP script, if anything
			       	type: "POST",
			        // Tell jQuery not to process data or worry about content-type
			        // You *must* include these options!
			        cache: false,
			        contentType: false,
			        processData: false,
			        data: form_data,                         
			        type: 'post',
				    success: function(resp){
				    	// zero out 
				    	self.zero_out(event);
					    if(resp){
					    	//check that we have a csv file in the response
					    	// check that we have data in the response
					    	if(resp.data.fl_fields && resp.data.user_fields && resp.data.csv_file){
					    		//looks like we have all our data

					    		// set the file name to the local var 
					    		self.server_file_path = resp.data.csv_file;
						    	// show the field matching section
						    	self.init_widget_matching(resp.data);

					    	}else{
					    		// we were missing a few fields. Show this errro
					    		self.set_error('Server response missing data. Please try again later', event);
					    	}

				    	}else{
				    			self.set_error('Server error, Please try again later', event);
				    	}


				    },
				    error: function(err, data) {

				    	if(err){
				    		self.set_error(err.responseJSON.message, event);
				    	}else{
				    		self.set_error('Server error please try again later', event);
				    	}
				    	
				    }
				});// end ajax 
			}// end else
		},// end upload_csv
	};// end app 
	app.init();

	// APP INTERFACE

	// widget_home  methods 
	// ==============================
	// ==============================

	/*
	* home_drop_hanler
	* @param event
	* calls the app home_drop_handler 
	*/
	home_drop_handler = function(event){
		app.home_drop_handler(event);
	};// end home_drop_handler
	/*
	* home_dragover_handler
	* @param event
	* calls the app home_dragover_handler
	*/
	home_dragover_handler = function(event){
		app.home_dragover_handler(event);
	};// end  home_dragover_handler

	/*
	* home_dragleave_handler
	* @param event
	* calls the app home_draglave_handler
	*/
	home_dragleave_handler = function(event){
		app.home_dragleave_handler(event);
	};// end home_dragleave_handler
	

	// widget_success widget_error methods 
	// ==============================
	// ==============================
	/*
	* reset_Form
	* @param event
	* callst he app zero_out method
	*/	
	reset_form = function(){
		app.zero_out();
	};

	// widget_matching methods 
	// ==============================
	// ==============================
	/*
	* matching_field_drag
	* @param event
	* callst he app matching_field_drag method
	*/
	matching_field_drag = function(event){
		app.matching_field_drag(event);
	};// end matching_field_drag
	/*
	* matching_drop
	* @param event
	* calls the app matching_drop  method 
	*/
	matching_drop = function(event){
		app.matching_drop(event);
	};// end matching_drop

	/*
	* matching_dragover
	* @param event
	* calls the matching allow_drop method
	*/
	matching_dragover = function(event){
		app.matching_dragover(event);
	};

	/*
	* remove_link 
	* @param event
	* calls the app.remove_link method
	*/
	remove_link = function(event){
		app.remove_link(event);
	};// end remove_link

	/*
	* submit_lnked_fields
	* 
	* calls th app.submit_linked_filds method
	*/
	submit_linked_fields = function(event){
		app.submit_linked_fields(event);
	};// end submit_linked_fields

//plugin code
})(jQuery)