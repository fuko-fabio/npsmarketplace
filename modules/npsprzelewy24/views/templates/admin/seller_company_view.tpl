{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<div id="container-seller-payment-settings">
    <div class="row">
        {*left*}
        <div class="col-lg-6">
            <div class="panel clearfix">
                <div class="panel-heading">
                    <i class="icon-user"></i>
                    {$company->company_name}
                    [{$company->id|string_format:"%06d"}]
                    -
                    <a href="mailto:{$company->email}"><i class="icon-envelope"></i>
                        {$company->email}
                    </a>
                </div>
                <div class="form-horizontal">
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='SPID' mod='npsprzelewy24'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$company->spid}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Registration Date' mod='npsprzelewy24'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$company->registration_date}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Company Name' mod='npsprzelewy24'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$company->company_name}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Person' mod='npsprzelewy24'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$company->person}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='City' mod='npsprzelewy24'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$company->city}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Street' mod='npsprzelewy24'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$company->street}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Post Code' mod='npsprzelewy24'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$company->post_code}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='NIP' mod='npsprzelewy24'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$company->nip}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='REGON' mod='npsprzelewy24'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$company->regon}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Bank Account' mod='npsprzelewy24'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$company->iban}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Accaptance of Przelewy24 Regulations' mod='npsprzelewy24'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                {if $company->acceptance}
                                    <span class="label label-success">
                                        <i class="icon-check"></i>
                                        {l s='Yes' mod='npsmarketplace'}
                                    </span>
                                {else}
                                    <span class="label label-danger">
                                        <i class="icon-remove"></i>
                                        {l s='No' mod='npsmarketplace'}
                                    </span>
                                {/if}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {*right*}
        <div class="col-lg-6">
            <div class="panel clearfix">

            </div>
        </div>
    </div>
</div>

<div id="container-dispatch-history">
    <div class="row">
        <div class="col-lg-12">
            <form class="container-command-top-spacing">
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-shopping-cart"></i>
                        {l s='Dispatch History' mod=npsmarketplace} <span class="badge">{$history|@count}</span>
                    </div>
                    <div class="table-responsive">

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
