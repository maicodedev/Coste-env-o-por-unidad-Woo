// Asegúrate de que exista un método de envío de tarifa plana
add_filter('woocommerce_shipping_methods', 'add_custom_shipping_method');

function add_custom_shipping_method($methods) {
    // Agrega el método de tarifa plana si no existe
    if (!isset($methods['flat_rate'])) {
        $methods['flat_rate'] = 'WC_Shipping_Flat_Rate';
    }
    return $methods;
}

// Modifica los métodos de envío disponibles
add_filter('woocommerce_package_rates', 'custom_modify_shipping_rates', 100, 2);

function custom_modify_shipping_rates($rates, $package) {
    // Solo para aplicar la lógica de envío si hay métodos de envío
    if (!empty($rates)) {
        // Obtiene la cantidad total de productos en el carrito
        $total_items = WC()->cart->get_cart_contents_count();

        // Si hay 5 o más unidades, establece el envío a 0
        if ($total_items >= 5) {
            foreach ($rates as $rate_key => $rate) {
                if ($rate->method_id === 'flat_rate') {
                    $rates[$rate_key]->cost = 0; // Establece el costo a 0
                    $rates[$rate_key]->label .= ' (Envío Gratis)'; // Añade etiqueta de "Envío Gratis"
                }
            }
        } else {
            // Si hay menos de 5 unidades, calcula el costo de envío en 10 € por unidad
            foreach ($rates as $rate_key => $rate) {
                if ($rate->method_id === 'flat_rate') {
                    $shipping_fee = $total_items * 10; // Cálculo del costo de envío
                    $rates[$rate_key]->cost = $shipping_fee; // Ajusta el costo
                    $rates[$rate_key]->label = 'Gastos de envío'; // Cambia la etiqueta
                }
            }
        }
    }
    return $rates; // Devuelve los métodos de envío modificados
}
