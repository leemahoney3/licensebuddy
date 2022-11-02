{extends file='admin/layouts/app.tpl'}

{block 'content'}

    <div class="row stat-labels margin-top-xl">
        <div class="col-md-2">
            <a href="{$moduleLink}&page={PageHelper::getPageInfo('licenses', 'slug')}&status=active">
                <div class="panel panel-success">
                    <div class="panel-body text-center">
                        <h1>{$licenses.active->count()}</h1>
                        Active Licenses
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{$moduleLink}&page={PageHelper::getPageInfo('licenses', 'slug')}&status=reissued">
                <div class="panel panel-info">
                    <div class="panel-body text-center">
                        <h1>{$licenses.reissued->count()}</h1>
                        Reissued Licenses
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{$moduleLink}&page={PageHelper::getPageInfo('licenses', 'slug')}&status=suspended">
                <div class="panel panel-warning">
                    <div class="panel-body text-center">
                        <h1>{$licenses.suspended->count()}</h1>
                        Suspended Licenses
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{$moduleLink}&page={PageHelper::getPageInfo('licenses', 'slug')}&status=expired">
                <div class="panel panel-danger">
                    <div class="panel-body text-center">
                        <h1>{$licenses.expired->count()}</h1>
                        Expired Licenses
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{$moduleLink}&page={PageHelper::getPageInfo('licenses', 'slug')}">
                <div class="panel panel-default">
                    <div class="panel-body text-center">
                        <h1>{$licenses.all->count()}</h1>
                        Total Licenses
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{$moduleLink}&page={PageHelper::getPageInfo('licenses', 'slug')}">
                <div class="panel panel-primary">
                    <div class="panel-body text-center">
                        <h1>{$licenses.last_accessed->count()}</h1>
                        Accessed Recently
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row margin-top-xl">

    <div class="col-md-4">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">License Search</h3>
        </div>
        <div class="panel-body">
            <form action="{$moduleLink}&page=licenses" method="post">
                <div class="form-group">
                    <label for="product">Product/License</label>
                    <select name="product" class="form-control" id="product" data-live-search="true">
                        <option value="0">Any Product/License</option>
                        {foreach $search.products as $product}
                            <option value="{$product->id}">{$product->name}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="form-group">
                    <label for="licenseKey">License Key</label>
                    <input type="text" name="license_key" class="form-control" id="licenseKey">
                </div>
                <div class="form-group">
                    <label for="domain">Domain Name</label>
                    <input type="text" name="domain" class="form-control" id="domain">
                </div>
                <div class="form-group">
                    <label for="directory">Directory</label>
                    <input type="text" name="directory" class="form-control" id="directory">
                </div>
                <div class="form-group">
                    <label for="ipAddress">IP Address</label>
                    <input type="text" name="ip_address" class="form-control" id="ipAddress">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control" id="status">
                        <option value="">Any Status</option>
                        <option value="Active">Active</option>
                        <option value="Reissued">Reissued</option>
                        <option value="Suspended">Suspended</option>
                        <option value="Expired">Expired</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="client">Client</label>
                    <select name="client" class="form-control" id="client" data-live-search="true">
                        <option value="">Any Client</option>
                        {foreach $search.clients as $client}
                            <option value="{$client->id}">#{$client->id} - {$client->firstName} {$client->lastName}</option>
                        {/foreach}
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Search Licenses</button>
            </form>
        </div>
    </div>
</div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Recently Purchased Licenses</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">License Key</th>
                                    <th scope="col">Service</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {if count($licenses.recent) == 0}
                                    <tr>
                                        <td colspan="5" class="text-center">No recent licenses</td>
                                    </tr>
                                {else}
                                    {foreach $licenses.recent as $license}
                                        <tr>
                                            <td><a href="{$moduleLink}&page=manage&id={$license->id}" target="_blank">{$license->id}</a></td>
                                            <td><a href="{$moduleLink}&page=manage&id={$license->id}" target="_blank">{$license->license_key}</a></td>
                                            <td><a href="clientsservices.php?userid={$license->getClient()->id}&id={$license->service->id}" target="_blank">{$license->service->product->name}</a></td>
                                            <td><a href="clientssummary.php?userid={$license->getClient()->id}" target="_blank">{$license->getClient()->firstname} {$license->getClient()->lastname}</a></td>
                                            <td>{$license->statusLabels()}</td>
                                        </tr>
                                    {/foreach}
                                {/if}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    
    </div>
    <div class="row margin-top-xl">

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Recent Logs</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">License Key</th>
                                    <th scope="col">Domain</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                {if count($logs.recent) == 0}
                                    <tr>
                                        <td colspan="5" class="text-center">No recent logs</td>
                                    </tr>
                                {else}
                                    {foreach $logs.recent as $log}
                                        <tr>
                                            <td><a href="{$moduleLink}&page=manage&id={$log->license->id}" target="_blank">{$log->license->license_key}</a></td>
                                            <td>{$log->domain}</td>
                                            <td>{$log->description}</td>
                                            <td>{$log->createdAt()}</td>
                                        </tr>
                                    {/foreach}
                                {/if}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Recent Blocks</h3>
                </div>
                <div class="panel-body">
                <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Domain/IP</th>
                            <th scope="col">Reason</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if count($blocks.recent) == 0}
                            <tr>
                                <td colspan="5" class="text-center">No recent blocks</td>
                            </tr>
                        {else}
                            {foreach $blocks.recent as $block}
                                <tr>
                                    <td>{$block->domain_ip}</td>
                                    <td>{$block->reason}</td>
                                    <td><a href="{$moduleLink}&page=blocks&action=remove&id={$block->id}" class="btn btn-danger btn-small">Remove Block</a></td>
                                </tr>
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
                </div>
            </div>
        </div>
    </div>

{/block}

{block "scripts"}
    <script src="{$systemURL}/modules/addons/licensebuddy/assets/js/bootstrap-select.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $('#product').selectpicker();
            $('#status').selectpicker();
            $('#client').selectpicker();
        });
    </script>
{/block}