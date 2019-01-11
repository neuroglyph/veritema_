function Product() {

}

//function Instructor(firstName, lastName, description, imageUrl) {
function Instructor(results) {
    self.Id = results.ID;
    self.FirstName = results.firstName;
    self.LastName = results.lastName;
    self.Description = results.description;
    self.ImageUrl = results.imageUrl;
}


// "main" employee viewmodel
function InstructorViewModel() {
    self.instructors = ko.observableArray([]);
    self.selectedInstructorId = ko.observable('');

    self.selectedInstructorId.subscribe(function (newValue) {
        // skip the first load or if ever undefined
        if (typeof (newValue) != 'undefined') {
            getOrders(newValue);
        }
    });
   
    // on model bind, fire service and load observable array
    $.ajax({
        url: '../api/Instructors/',
        type: 'GET',
        async: true,
        dataType: "json",
        contentType: 'application/json; charset=utf-8',
        success: function (result) {
            for (var i = 0; i < result.length; i++) {
                var item = new Instructor(result[i]);
                self.instructors.push(item);
            } 
        },
        error: function (err) {
            alert('error!');
        }
    });

    self.getDescription = function (item) {
    }
}

// additional functions

// could use much more logic testing here but for illustrative purposes this should suffice
// also, alert() is poor design choice for presenting info to user. if more time, would use bootstrap modals
function LoadMsg(data, status, xhr)  {
  alert(data.msg);
}

