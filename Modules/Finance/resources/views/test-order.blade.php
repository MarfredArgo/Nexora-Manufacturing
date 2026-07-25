<div>
    <form method="POST" action="/orders">

@csrf

<input name="order_id" value="ORD-013">

<input name="customer_name" value="Juan Dela Cruz">

<input name="address" value="Imus Cavite">

<input name="due_date" value="2026-08-20">

<input name="items[0][product_name]" value="Laptop">

<input name="items[0][qty]" value="2">

<input name="items[0][product_amount]" value="35000">


<button type="submit">
Create Order
</button>

</form>
</div>
