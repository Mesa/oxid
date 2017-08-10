[{capture append="oxidBlock_content"}]
    [{assign var="template_title" value="MY_GIFT_REGISTRY"|oxmultilangassign}]
    [{if !$oView->getWishListUsers() && $oView->getWishListSearchParam()}]
        [{assign var="_statusMessage" value="MESSAGE_SORRY_NO_GIFT_REGISTRY"|oxmultilangassign}]
        [{include file="message/error.tpl" statusMessage=$_statusMessage}]
    [{/if}]
    [{assign var="editval" value=$oView->getEnteredData()}]
    [{if $oView->isWishListEmailSent()}]
        [{assign var="_statusMessage" value="GIFT_REGISTRY_SENT_SUCCESSFULLY"|oxmultilangassign:$editval->rec_email}]
        [{include file="message/notice.tpl" statusMessage=$_statusMessage}]
    [{/if}]

    <h1 class="page-header">[{$oView->getTitle()}]</h1>

    <div class="wishlist-search">
        [{include file="form/wishlist_search.tpl" searchClass="account_wishlist"}]
    </div>

    <hr>

    <div class="wishlist">
        [{if $oView->getWishList()}]
            [{include file="form/wishlist_publish.tpl"}]
            <hr>
            [{include file="form/wishlist_suggest.tpl"}]
        [{/if}]
    </div>

    [{if $oView->getWishList()}]
        [{include file="widget/product/list.tpl" type="line" listId="wishlistProductList" title="" products=$oView->getWishProductList() removeFunction="towishlist" toBasketFunction="tobasket" owishid=$oxcmp_user->oxuser__oxid->value}]
    [{else}]
        <p class="alert alert-info">
            [{oxmultilang ident="GIFT_REGISTRY_EMPTY"}]
        </p>
    [{/if}]
    [{insert name="oxid_tracker" title=$template_title}]
[{/capture}]
[{capture append="oxidBlock_sidebar"}]
    [{include file="page/account/inc/account_menu.tpl" active_link="wishlist"}]
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]