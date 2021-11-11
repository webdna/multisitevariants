/**
 * Multisite Variants plugin for Craft CMS 3.x
 *
 * Multisite Variants JS
 *
 * @author    Web DNA
 * @copyright Copyright (c) 2021 Web DNA
 * @link      https://webdna.co.uk
 * @package   MultisiteVariants
 * @since     1.0.0
 */

let destinations = document.querySelectorAll(`.variant-matrixblock[data-id]`);

destinations.forEach( d => {
    let variantId = d.dataset.id,
        $metafields = d.querySelector(`.fields .variant-properties.meta`),
        $switch = document.querySelector(`#variants-${ variantId }-enabledForSite-field`);
        $metafields.appendChild($switch);
});
