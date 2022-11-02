{extends file='admin/layouts/app.tpl'}

{block "content"}
    <div class="row">
        <div class="col-md-8 col-md-offset-2 text-center margin-top-xl">
            {if PageHelper::getAttribute('success')}
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>Success!</strong> The license has been updated
                        </div>
                    </div>
                </div>
            {/if}

            <h1>Manage License <b>{$license->license_key}</b></h1>
                
            <p><a href="clientssummary.php?userid={$license->getClient()->id}" target="_blank" class="btn btn-default"><i class="glyphicon glyphicon-user"></i> #{$license->getClient()->id} - {$license->getClient()->firstName} {$license->getClient()->lastName}</a></p>
            
            <div class="panel panel-default margin-top-xl col-md-4">
                <div class="panel-body text-left">
                    <form action="{$moduleLink}&page=manage&id={$license->id}" method="post">
                        <div class="form-group">
                            <label for="product">Product/License</label>
                            <div class="input-group">
                                <input type="text" name="product" class="form-control" id="product" value="{$license->service->product->name}" disabled>
                                <span class="input-group-btn">
                                    <a href="clientshosting.php?userid={$license->service->userid}&id={$license->service->id}" target="_blank" class="btn btn-default">View Product/License &raquo;</a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="domain">Allowed Domains</label>
                            <input type="text" name="allowed_domains" class="form-control" id="domain" value="{$license->allowed_domains}">
                        </div>
                        <div class="form-group">
                            <label for="directory">Allowed Directory</label>
                            <input type="text" name="allowed_directory" class="form-control" id="directory" value="{$license->allowed_directory}">
                        </div>
                        <div class="form-group">
                            <label for="ipAddress">Allowed Domains</label>
                            <input type="text" name="allowed_ip_address" class="form-control" id="ipAddress" value="{$license->allowed_ip_address}">
                        </div>
                        <div class="form-group">
                            <label for="reissueCount">Reissue Count</label>
                            <input type="text" name="reissue_count" class="form-control" id="reissueCount" value="{$license->reissue_count}">
                        </div>
                        <div class="form-group">
                            <label for="ipAddress">Status</label>
                            <select name="status" class="form-control">
                            <option value="Active"{if $license->status == 'Active'} selected{/if}>Active</option>
                                <option value="Reissued"{if $license->status == 'Reissued'} selected{/if}>Reissued</option>
                                <option value="Suspended"{if $license->status == 'Suspended'} selected{/if}>Suspended</option>
                                <option value="Expired"{if $license->status == 'Expired'} selected{/if}>Expired</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="lastAccessed">Last Accessed</label>
                            <input type="text" name="last_accessed" class="form-control" id="lastAccessed" value="{$license->lastAccessedAt()}" disabled>
                        </div>
                        <div class="col-md-12 text-center margin-top-lg">
                            <input type="submit" name="save" class="btn btn-primary" value="Save Changes" />
                            <a href="{$moduleLink}&page=dashboard" class="btn btn-default">Discard Changes</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel panel-default margin-top-xl col-md-7 margin-left-lg text-left col-sm-6">
                <h4 class="margin-top-lg margin-bottom-lg">Recent Access Logs</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Date/Time</th>
                                <th scope="col">Domain</th>
                                <th scope="col">Directory</th>
                                <th scope="col">IP</th>
                                <th scope="col">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if count($logs) == 0}
                                <tr>
                                    <td colspan="5" class="text-center">No recent access logs</td>
                                </tr>
                            {else}
                                {foreach $logs as $log}
                                    <tr>
                                        <td>{$log->createdAt()}</td>
                                        <td>{$log->domain}</td>
                                        <td>{$log->directory}</td>
                                        <td>{$log->ip_address}</td>
                                        <td>{$log->description}</td>
                                    </tr>
                                {/foreach}
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
{/block}