$(document).ready(function() {
  $("#clientForm").on("submit", function(event) {
      event.preventDefault();

      var clientName = $("#clientName").val();
      var billingType = $("#billingType").val();
      var billingRate = parseFloat($("#billingRate").val());

      var formData = {
          clientName: clientName,
          billingType: billingType,
          billingRate: billingRate
      };

      $.ajax({
          url: "/api/addClient",
          type: "POST",
          contentType: "application/json; charset=utf-8",
          data: JSON.stringify(formData),
          dataType: "json",
          success: function(response) {
              // Handle the response from the server here
              alert("Client added successfully!");
          },
          error: function(xhr, status, error) {
              // Handle errors here
              alert("An error occurred while adding the client.");
          }
      });
  });
});