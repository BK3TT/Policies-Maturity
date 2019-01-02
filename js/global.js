$(document).ready(function()
{
    loadPolicyData();

    $("button#exportToXML").hide();
});

function loadPolicyData()
{
    $.get("api/v2/Policies/viewPolicyData/", function(data) 
    {
        var returnedData = JSON.parse(data);

        for(var x = 0; x < returnedData.length; x++)
        {
            var html = '<div class="col-sm-4 col-md-4"> \
                <div class="panel panel-default text-center"> \
                <div class="panel-heading">' + returnedData[x].policy_number + '</div> \
                <div class="panel-body"> \
                <p><b>Discretionary_bonus: </b>' + returnedData[x].discretionary_bonus + '</p> \
                <p><b>Membership: </b>' + returnedData[x].membership + '</p> \
                <p><b>Premiums: </b>' + returnedData[x].premiums + '</p> \
                <p><b>Uplift Percentage: </b>' + returnedData[x].uplift_percentage + '</p> \
                </div> \
                <div class="panel-footer"> \
                <button id="' + x + '" type="button" class="btn btn-primary singlePolicy">Select Policy</button> \
                </div> \
                </div> \
                </div>';

            $("#policy-cards").append(html);
        }

        $("button.singlePolicy").on("click", function()
        {
            $(this).toggleClass("selectedPolicy");

            ($("button.selectedPolicy").length > 0) ? $("button#exportToXML").show() : $("button#exportToXML").hide();
        
            ($(this).hasClass("selectedPolicy")) ? $(this).text("Policy Selected") : $(this).text("Select Policy");
        });
    });

    setTimeout(function()
    {
        $(".loader-container").fadeOut(500);
    }, 2000);

    exportAction();
}

function exportAction()
{
    $("button#exportToXML").on("click", function()
    {
        var policyIds = $('button.selectedPolicy').map(function() 
        {
            return $(this).attr('id');
        }).get();
        
        exportPolicyXML(policyIds.join(','));
    });
}

function exportPolicyXML(policyIds)
{
    $.get("api/v2/Policies/policyMaturity/" + policyIds, function(data) 
    {
        var response = JSON.parse(data);

        (response.status === "success") ? console.log("XML generated successfully") :  console.log("XML failed to be generated");
    });
}