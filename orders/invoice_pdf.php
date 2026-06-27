<?php

require("../config/database.php");
require("../vendor/autoload.php");

use Dompdf\Dompdf;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Order");
}

$order_id = $_GET['id'];

$orderResult = pg_query_params($conn, "
SELECT 
    o.order_id,
    o.order_date,
    o.total_amount,
    o.payment_status,
    o.payment_mode,
    c.customer_name,
    c.phone,
    c.email,
    c.address
FROM orders o
LEFT JOIN customers c ON o.customer_id = c.customer_id
WHERE o.order_id = $1
", array($order_id));

if (!$orderResult || pg_num_rows($orderResult) == 0) {
    die("Order not found");
}

$order = pg_fetch_assoc($orderResult);

$itemResult = pg_query_params($conn, "
SELECT 
    oi.quantity,
    oi.price,
    oi.subtotal,
    p.product_name,
    p.brand
FROM order_items oi
LEFT JOIN products p ON oi.product_id = p.product_id
WHERE oi.order_id = $1
", array($order_id));

$packageResult = pg_query_params($conn, "
SELECT 
    op.package_price,
    op.remarks,
    fp.product_name AS frame_name,
    fp.brand AS frame_brand,
    lt.lens_name,
    lt.price AS lens_price,
    lc.coating_name,
    lc.price AS coating_price
FROM order_packages op
LEFT JOIN products fp ON op.frame_product_id = fp.product_id
LEFT JOIN lens_types lt ON op.lens_id = lt.lens_id
LEFT JOIN lens_coatings lc ON op.coating_id = lc.coating_id
WHERE op.order_id = $1
", array($order_id));

$package = ($packageResult && pg_num_rows($packageResult) > 0)
    ? pg_fetch_assoc($packageResult)
    : null;

$html = '
<h2 style="text-align:center;">Clarity Optical Store</h2>
<h3 style="text-align:center;">Invoice</h3>

<hr>

<table width="100%">
<tr>
<td>
<b>Clarity Optical Store</b><br>
Chennai, Tamil Nadu<br>
Phone: 9876543210
</td>

<td style="text-align:right;">
<b>Invoice No:</b> #' . htmlspecialchars($order['order_id']) . '<br>
<b>Date:</b> ' . date("d M Y, h:i A", strtotime($order['order_date'])) . '<br>
<b>Status:</b> ' . htmlspecialchars($order['payment_status']) . '<br>
<b>Payment Mode:</b> ' . htmlspecialchars($order['payment_mode'] ?? '-') . '
</td>
</tr>
</table>

<hr>

<h4>Bill To</h4>
<p>
<b>Name:</b> ' . htmlspecialchars($order['customer_name'] ?? 'Deleted Customer') . '<br>
<b>Phone:</b> ' . htmlspecialchars($order['phone'] ?? '-') . '<br>
<b>Email:</b> ' . htmlspecialchars($order['email'] ?? '-') . '<br>
<b>Address:</b> ' . htmlspecialchars($order['address'] ?? '-') . '
</p>
';

if ($package) {

    $firstItem = pg_fetch_assoc($itemResult);
    $frameAmount = $firstItem ? $firstItem['subtotal'] : 0;

    $html .= '
    <h4>Optical Package Details</h4>

    <table border="1" width="100%" cellspacing="0" cellpadding="8">
    <tr>
        <th>Component</th>
        <th>Details</th>
        <th>Amount</th>
    </tr>

    <tr>
        <td>Frame</td>
        <td>' . htmlspecialchars($package['frame_name'] ?? 'Deleted Frame') . '<br>
        <small>' . htmlspecialchars($package['frame_brand'] ?? '-') . '</small></td>
        <td>₹' . number_format($frameAmount, 2) . '</td>
    </tr>

    <tr>
        <td>Lens</td>
        <td>' . htmlspecialchars($package['lens_name'] ?? 'No Lens') . '</td>
        <td>₹' . number_format($package['lens_price'] ?? 0, 2) . '</td>
    </tr>

    <tr>
        <td>Coating</td>
        <td>' . htmlspecialchars($package['coating_name'] ?? 'No Coating') . '</td>
        <td>₹' . number_format($package['coating_price'] ?? 0, 2) . '</td>
    </tr>

    <tr>
        <td>Remarks</td>
        <td colspan="2">' . htmlspecialchars($package['remarks'] ?? '-') . '</td>
    </tr>

    <tr>
        <td colspan="2" style="text-align:right;"><b>Grand Total</b></td>
        <td><b>₹' . number_format($order['total_amount'], 2) . '</b></td>
    </tr>

    </table>
    ';

} else {

    $html .= '
    <h4>Product Details</h4>

    <table border="1" width="100%" cellspacing="0" cellpadding="8">
    <tr>
        <th>Product</th>
        <th>Brand</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Subtotal</th>
    </tr>
    ';

    while ($item = pg_fetch_assoc($itemResult)) {
        $html .= '
        <tr>
            <td>' . htmlspecialchars($item['product_name'] ?? 'Deleted Product') . '</td>
            <td>' . htmlspecialchars($item['brand'] ?? '-') . '</td>
            <td>₹' . number_format($item['price'], 2) . '</td>
            <td>' . htmlspecialchars($item['quantity']) . '</td>
            <td>₹' . number_format($item['subtotal'], 2) . '</td>
        </tr>
        ';
    }

    $html .= '
    <tr>
        <td colspan="4" style="text-align:right;"><b>Total</b></td>
        <td><b>₹' . number_format($order['total_amount'], 2) . '</b></td>
    </tr>
    </table>
    ';
}

$html .= '
<br><br>

<p style="text-align:center;">
Thank you for choosing Clarity Optical Store.
</p>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$dompdf->stream("Invoice_" . $order_id . ".pdf", ["Attachment" => true]);

?>