<div class="mt-3">
    <h4>Client Details</h4>

    <!-- Personal Details Section -->
    <h5>Personal Details</h5>
    <div class="row">
        <div class="col-md-3">
            <label>Client Code:</label>
            <div><strong>{{ $client->client_code }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>User ID:</label>
            <div><strong>{{ $client->user_id }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Username:</label>
            <div><strong>{{ $client->username }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Name:</label>
            <div><strong>{{ $client->name }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Phone Number:</label>
            <div><strong>{{ $client->phone_no }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Mobile:</label>
            <div><strong>{{ $client->mobile }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Email:</label>
            <div><strong>{{ $client->email }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Country:</label>
            <div><strong>{{ $client->country }}</strong></div>
        </div>
    </div>

    <!-- Account Details Section -->
    <h5 class="mt-4">Account Details</h5>
    <div class="row">
        <div class="col-md-3">
            <label>Brand ID:</label>
            <div><strong>{{ $client->brand_id }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Created By:</label>
            <div><strong>{{ $client->createdBy }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Relationship Manager:</label>
            <div><strong>{{ $client->rm }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Status:</label>
            <div><strong>{{ $client->status }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Account Type:</label>
            <div><strong>{{ $client->accountType }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Is Demo:</label>
            <div><strong>{{ $client->isDemo }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Open Date:</label>
            <div><strong>{{ $client->openDate }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Terms Accepted Date:</label>
            <div><strong>{{ $client->termsAcceptedDate }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Terms Accepted IP:</label>
            <div><strong>{{ $client->termsAcceptedIP }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Terms Accepted:</label>
            <div><strong>{{ $client->termsAccepted }}</strong></div>
        </div>
    </div>

    <!-- Policy Details Section -->
    <h5 class="mt-4">Policy Details</h5>
    <div class="row">
        <div class="col-md-3">
            <label>Currencies Policies ID:</label>
            <div><strong>{{ $client->currenciesPoliciesID }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Generic Policies ID:</label>
            <div><strong>{{ $client->genericPoliciesID }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Ignore Liquidation:</label>
            <div><strong>{{ $client->ignoreLiquidation }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Close Only:</label>
            <div><strong>{{ $client->closeOnly }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Open Only:</label>
            <div><strong>{{ $client->openOnly }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Trading Type:</label>
            <div><strong>{{ $client->tradingType }}</strong></div>
        </div>
    </div>

    <!-- Additional Details Section -->
    <h5 class="mt-4">Additional Details</h5>
    <div class="row">
        <div class="col-md-3">
            <label>Block Frequent Trades Seconds:</label>
            <div><strong>{{ $client->blockFrequentTradesSeconds }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Validate Money Before Entry:</label>
            <div><strong>{{ $client->validateMoneyBeforeEntry }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Validate Money Before Close:</label>
            <div><strong>{{ $client->validateMoneyBeforeClose }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Client Price Execution:</label>
            <div><strong>{{ $client->clientPriceExecution }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Percentage Level 1:</label>
            <div><strong>{{ $client->percentageLevel1 }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Percentage Level 2:</label>
            <div><strong>{{ $client->percentageLevel2 }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Percentage Level 3:</label>
            <div><strong>{{ $client->percentageLevel3 }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Percentage Level 4:</label>
            <div><strong>{{ $client->percentageLevel4 }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Credit Loan Percentage:</label>
            <div><strong>{{ $client->creditLoanPercentage }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Parent ID:</label>
            <div><strong>{{ $client->parentId }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Currency Sign:</label>
            <div><strong>{{ $client->currencySign }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Account ID Prefix:</label>
            <div><strong>{{ $client->accountIdPrefix }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Enable Cash Delivery:</label>
            <div><strong>{{ $client->enableCashDelivery }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Enable Deposit Request:</label>
            <div><strong>{{ $client->enableDepositRequest }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Allow Multi-Session:</label>
            <div><strong>{{ $client->allowMultiSession }}</strong></div>
        </div>
        <div class="col-md-3">
            <label>Liquidated:</label>
            <div><strong>{{ $client->liquidated }}</strong></div>
        </div>
    </div>
</div>