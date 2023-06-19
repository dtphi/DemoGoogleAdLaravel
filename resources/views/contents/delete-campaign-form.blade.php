<div class="card mt-3">
    <div class="card-header">
        Delete a campaign of the specified customer ID
    </div>
    <div class="card-body">
        <form action="/delete-campaign" method="POST">
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="customerId" class="col-sm-2 col-form-label">Customer ID</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="customerId" name="customerId"
                           aria-describedby="customerIdHelp"
                           placeholder="Enter your customer ID" required>
                    <small id="customerIdHelp" class="form-text text-muted">The ID of the
                        customer account to pause the campaign from, e.g., 1234567890.
                    </small>
                </div>
            </div>
            <div class="form-group row">
                <label for="campaignId" class="col-sm-2 col-form-label">Campaign ID</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="campaignId" name="campaignId"
                           aria-describedby="campaignIdHelp"
                           placeholder="Enter your campaign ID" required>
                    <small id="campaignIdHelp" class="form-text text-muted">The ID of the
                        campaign to pause, e.g., 1234567890.
                    </small>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-danger">Delete Campaign</button>
                </div>
            </div>
        </form>
    </div>
</div>
