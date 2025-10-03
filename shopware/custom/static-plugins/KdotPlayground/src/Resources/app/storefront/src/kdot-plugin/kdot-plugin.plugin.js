import Plugin from 'src/plugin-system/plugin.class';

export default class KdotPlugin extends Plugin {
    init() {
        this.onLoad();
    }

    onLoad() {
        fetch('/kdot/products', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data && Array.isArray(data)) {
                const productIds = data.map(product => product.id);

                this.productIds = productIds;

                const productBoxes = document.querySelectorAll('.product-box');
                productBoxes.forEach(productBox => {
                    try {
                        const productCard = productBox.querySelector('.card-body');
                        const productElement = productBox.getAttribute('data-product-information');
                        if (productCard) {
                            if (productElement) {
                                const productInfo = JSON.parse(productElement);
                                const elementProductId = productInfo.id;
                                if (productIds.includes(elementProductId)) {
                                    console.log('Product bought');
                                    const badgeDiv = productBox.querySelector('.product-badges');
                                    badgeDiv.querySelector('.kdot-bought').removeAttribute('style');
                                }
                            }
                        }
                    } catch (parseError) {
                        console.warn('Error parsing product information:', parseError);
                    }
                });
            } else {
                console.warn('Response is not an array or missing data');
            }
        })
        .catch(error => {
            console.error('Error fetching products:', error);
        });
    }
}
