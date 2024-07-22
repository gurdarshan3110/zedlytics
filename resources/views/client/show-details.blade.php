<div class="mt-3">
    <h4>Client Details</h4>
    <div class="row">
        <!-- Personal Details Section -->
        <div class="col-md-3">
            <h5 class="p-1">Personal Details</h5>
            <div class="card card-fixed-height w-100 p-2">
                <div class="form-group">
                    <label>Client Code:</label>
                    <div class="card-value">{{ $client->client_code }}</div>
                </div>
                <div class="form-group">
                    <label>Parent:</label>
                    <div class="card-value">{{ (($client->parentId!=0)?$client->parent->name:'NA') }}</div>
                </div>
                <div class="form-group">
                    <label>Username:</label>
                    <div class="card-value">{{ $client->username }}</div>
                </div>
                <div class="form-group">
                    <label>Name:</label>
                    <div class="card-value">{{ $client->name }}</div>
                </div>
                <div class="form-group">
                    <label>Phone Number:</label>
                    <div class="card-value">{{ $client->phone_no }}</div>
                </div>
                <div class="form-group">
                    <label>Mobile:</label>
                    <div class="card-value">{{ $client->mobile }}</div>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <div class="card-value">{{ $client->email }}</div>
                </div>
                <div class="form-group">
                    <label>Country:</label>
                    <div class="card-value">{{ $client->country }}</div>
                </div>
            </div>
        </div>
        <!-- Account Details Section -->
        <div class="col-md-3">
            <h5 class="p-1">Account Details</h5>
            <div class="card card-fixed-height w-100 p-2">
                <div class="form-group">
                    <label>Brand:</label>
                    <div class="card-value">{{ $client->brand->name }}</div>
                </div>
                <div class="form-group">
                    <label>Created By:</label>
                    <div class="card-value">{{ $client->createdBy }}</div>
                </div>
                <div class="form-group">
                    <label>Relationship Manager:</label>
                    <div class="card-value">{{ (($client->rm==0 || $client->rm==null || $client->rm=='')?'NA':$client->rmanager->name) }}</div>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <div class="card-value">{{ $client->status }}</div>
                </div>
                <div class="form-group">
                    <label>Account Type:</label>
                    <div class="card-value">{{ $client->accountType }}</div>
                </div>
                <div class="form-group">
                    <label>Is Demo:</label>
                    <div class="card-value">{{ $client->isDemo }}</div>
                </div>
                <div class="form-group">
                    <label>Open Date:</label>
                    <div class="card-value">{{ $client->openDate }}</div>
                </div>
                <div class="form-group">
                    <label>Terms Accepted Date:</label>
                    <div class="card-value">{{ $client->termsAcceptedDate }}</div>
                </div>
                <div class="form-group">
                    <label>Terms Accepted IP:</label>
                    <div class="card-value">{{ $client->termsAcceptedIP }}</div>
                </div>
                <div class="form-group">
                    <label>Terms Accepted:</label>
                    <div class="card-value">{{ $client->termsAccepted }}</div>
                </div>
            </div>
        </div>
        <!-- Policy Details Section -->
        <div class="col-md-3">
            <h5 class="p-1">Policy Details</h5>
            <div class="card card-fixed-height w-100 p-2">
                <div class="form-group">
                    <label>Currencies Policies ID:</label>
                    <div class="card-value">{{ $client->currency_policies_names }}</div>
                </div>
                <div class="form-group">
                    <label>Generic Policies ID:</label>
                    <div class="card-value">{{ $client->generic_policies_name }}</div>
                </div>
                <div class="form-group">
                    <label>Ignore Liquidation:</label>
                    <div class="card-value">{{ $client->ignoreLiquidation }}</div>
                </div>
                <div class="form-group">
                    <label>Close Only:</label>
                    <div class="card-value">{{ $client->closeOnly }}</div>
                </div>
                <div class="form-group">
                    <label>Open Only:</label>
                    <div class="card-value">{{ $client->openOnly }}</div>
                </div>
                <div class="form-group">
                    <label>Trading Type:</label>
                    <div class="card-value">{{ $client->tradingType }}</div>
                </div>
            </div>
        </div>
        <!-- Additional Details Section -->
        <div class="col-md-3">
            <h5 class="p-1">Additional Details</h5>
            <div class="card card-fixed-height w-100 p-2">
                <div class="form-group">
                    <label>Block Frequent Trades Seconds:</label>
                    <div class="card-value">{{ $client->blockFrequentTradesSeconds }}</div>
                </div>
                <div class="form-group">
                    <label>Validate Money Before Entry:</label>
                    <div class="card-value">{{ $client->validateMoneyBeforeEntry }}</div>
                </div>
                <div class="form-group">
                    <label>Validate Money Before Close:</label>
                    <div class="card-value">{{ $client->validateMoneyBeforeClose }}</div>
                </div>
                <div class="form-group">
                    <label>Client Price Execution:</label>
                    <div class="card-value">{{ $client->clientPriceExecution }}</div>
                </div>
                <div class="form-group">
                    <label>Percentage Level 1:</label>
                    <div class="card-value">{{ $client->percentageLevel1 }}</div>
                </div>
                <div class="form-group">
                    <label>Percentage Level 2:</label>
                    <div class="card-value">{{ $client->percentageLevel2 }}</div>
                </div>
                <div class="form-group">
                    <label>Percentage Level 3:</label>
                    <div class="card-value">{{ $client->percentageLevel3 }}</div>
                </div>
                <div class="form-group">
                    <label>Percentage Level 4:</label>
                    <div class="card-value">{{ $client->percentageLevel4 }}</div>
                </div>
                <div class="form-group">
                    <label>Credit Loan Percentage:</label>
                    <div class="card-value">{{ $client->creditLoanPercentage }}</div>
                </div>
                <div class="form-group">
                    <label>User ID:</label>
                    <div class="card-value">{{ $client->user_id }}</div>
                </div>
                <div class="form-group">
                    <label>Currency Sign:</label>
                    <div class="card-value">{{ $client->currencySign }}</div>
                </div>
                <div class="form-group">
                    <label>Account ID Prefix:</label>
                    <div class="card-value">{{ $client->accountIdPrefix }}</div>
                </div>
                <div class="form-group">
                    <label>Enable Cash Delivery:</label>
                    <div class="card-value">{{ $client->enableCashDelivery }}</div>
                </div>
                <div class="form-group">
                    <label>Enable Deposit Request:</label>
                    <div class="card-value">{{ $client->enableDepositRequest }}</div>
                </div>
                <div class="form-group">
                    <label>Allow Multi-Session:</label>
                    <div class="card-value">{{ $client->allowMultiSession }}</div>
                </div>
                <div class="form-group">
                    <label>Liquidated:</label>
                    <div class="card-value">{{ $client->liquidated }}</div>
                </div>
            </div>
        </div>
    </div>
</div>