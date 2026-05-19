var inventory = [
    {id: 1, name:"Lenove Legion 5a", price: 1529.99, stock: 7 },
    {id: 2, name:"Ipad Pro M4", price: 999.00, stock: 22 },
    {id: 3, name:"Sony WH-1000XM5", price: 399.99, stock: 20 },
    {id: 4, name:"Wooting 80HE TenZ Takeover", price: 223.68, stock: 6 },
    {id: 5, name:"Xiaomi 17 Ultra", price: 1299.00, stock: 0 }
];

var nextId = 6;

function renderTable() {
    var tbody = document.getElementById("tableBody");
    tbody.innerHTML = "";

  for (var i = 0; i < inventory.length; i++) {
    var item = inventory[i];
    var tr = document.createElement("tr");

    var stocks = "";
    if (item.stock === 0) {
      stocks = "<span class='out-of-stock'> <b>Out of Stock</b> </span>";
    } else {
      stocks = item.stock;
    }

    tr.innerHTML =
      "<td>" + item.id + "</td>" +
      "<td>" + item.name + "</td>" +
      "<td>$" + item.price.toFixed(2) + "</td>" +
      "<td>" + stocks + "</td>";

    tbody.appendChild(tr);
  }
}

function addProduct() {
  var nameInput  = document.getElementById("productName");
  var priceInput = document.getElementById("productPrice");
  var stockInput = document.getElementById("productStock");

  var name  = nameInput.value.trim();
  var price = parseFloat(priceInput.value);
  var stock = parseInt(stockInput.value);

  if (name === "" || priceInput.value === "" || stockInput.value === "") {
    alert("Fill in all fields");
    return;
  }

  if (price < 0 || stock < 0) {
    alert("Numbers cannot be negative");
    return;
  }

  var newProduct = {
    id: nextId,
    name: name,
    price: price,
    stock: stock
  };

  inventory.push(newProduct);
  nextId = nextId + 1;

  nameInput.value  = "";
  priceInput.value = "";
  stockInput.value = "";

  renderTable();
}

document.getElementById("addBtn").addEventListener("click", addProduct);
renderTable();
