{extends file='admin/layouts/app.tpl'}

{block "content"}

    <div class="row">
        <div class="col-md-12">
            <h2>Search Licenses</h2>
            <div class="panel panel-default margin-top-lg">
                <div class="panel-body">
                    <form action="{$moduleLink}&page=licenses" method="post" class="form-inline">
                        <div class="form-group margin-right-lg">
                            <label for="productLicense">Product/License</label>
                            <select name="product" class="form-control" id="product" data-live-search="true">
                                <option value="0">Any Product/License</option>
                                {foreach $searchForm.products as $product}
                                    <option value="{$product->id}" {if $request.product == $product->id}selected{/if}>{$product->name}</option>
                                {/foreach}
                            </select>
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="licenseKey">License Key</label>
                            <input type="text" name="license_key" class="form-control" id="licenseKey" value="{$request.license_key}">
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="domain">Domain Name</label>
                            <input type="text" name="domain" class="form-control" id="domain" value="{$request.domain}">
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="directory">Directory</label>
                            <input type="text" name="directory" class="form-control" id="directory" value="{$request.directory}">
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="ipAddress">IP Address</label>
                            <input type="text" name="ip_address" class="form-control" id="ipAddress" value="{$request.ip_address}">
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="status">Status</label>
                            <select name="status" class="form-control" id="status">
                                <option value="">Any Status</option>
                                <option value="Active" {if $request.status == 'Active' || PageHelper::getAttribute('status') == 'active'}selected{/if}>Active</option>
                                <option value="Reissued" {if $request.status == 'Reissued' || PageHelper::getAttribute('status') == 'reissued'}selected{/if}>Reissued</option>
                                <option value="Suspended" {if $request.status == 'Suspended' || PageHelper::getAttribute('status') == 'suspended'}selected{/if}>Suspended</option>
                                <option value="Expired" {if $request.status == 'Expired' || PageHelper::getAttribute('status') == 'expired'}selected{/if}>Expired</option>
                            </select>
                        </div>

                        <div class="form-group margin-right-lg">
                            <label for="client">Client</label>
                            <select name="client" class="form-control" id="client" data-live-search="true">
                                <option value="">Any Client</option>
                                {foreach $searchForm.clients as $client}
                                    <option value="{$client->id}" {if $request.client == $client->id}selected{/if}>#{$client->id} - {$client->firstName} {$client->lastName}</option>
                                {/foreach}
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Search Licenses</button>
                        <a href="{$moduleLink}&page=licenses" class="btn btn-default">Clear</a>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {if $search}
                <h2>Search Results</h2>
            {else}
                <h2>{$type} Licenses</h2>
            {/if}
            
        </div>
    </div>

    <div class="row margin-top-lg">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                           <th scope="col">ID</th>
                           <th scope="col">License Key</th>
                           <th scope="col">Allowed Domains</th>
                           <th scope="col">Allowed Directory</th>
                           <th scope="col">Allowed IP</th>
                           <th scope="col">Reissue Count</th>
                           <th scope="col">Status</th>
                           <th scope="col">Client</th>
                           <th scope="col">Related Service</th>
                           <th scope="col">Last Accessed</th>
                           <th scope="col">Created Date/Time</th> 
                           <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if count($licenses.data) == 0}
                            <tr>
                                <td colspan="11" class="text-center">No {($type != 'All') ? strtolower($type) : ''} licenses found</td>
                            </tr>
                        {else}
                            {foreach $licenses.data as $license}
                                <tr>
                                    <td><a href="{$moduleLink}&page=manage&id={$license->id}" target="_blank">{$license->id}</a></td>
                                    <td><a href="{$moduleLink}&page=manage&id={$license->id}" target="_blank">{$license->license_key}</a></td>
                                    <td>{($license->allowed_domains == '') ? '-' : $license->allowed_domains}</td>
                                    <td>{($license->allowed_directory == '') ? '-' : $license->allowed_directory}</td>
                                    <td>{($license->allowed_ip_address == '') ? '-' : $license->allowed_ip_address}</td>
                                    <td>{$license->reissue_count}</td>
                                    <td>{$license->statusLabels()}</td>
                                    <td><a href="clientssummary.php?userid={$license->getClient()->id}" target="_blank">{$license->getClient()->firstname} {$license->getClient()->lastname}</a></td>
                                    <td><a href="clientsservices.php?userid={$license->getClient()->id}&id={$license->service->id}" target="_blank">{$license->service->product->name}</a></td>
                                    <td>{$license->lastAccessedAt()}</td>
                                    <td>{$license->createdAt()}</td>
                                    <td><a href="{$moduleLink}&page=manage&id={$license->id}" class="btn btn-primary">Manage</a></td>
                                </tr>
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
            {$licenses.links}
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