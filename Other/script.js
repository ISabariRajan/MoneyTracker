sample_data = {
  "spend": {
    date: "12-12-2024",
    description: "Sample Item",
    item: "Sample Item",
    price: 11233,
    quantity: 2,
    total: 1982
  },
  "clients": {
    name: "SSSS",
    type: "monthly",
    rate: 600,
    pending: 600
  },
  "income": {}
}

const populate_table = (div_id) => {
  table_data = [sample_data[div_id]]
  table_id = div_id + " table"
  output = create_table_columns_from_data(table_data[0], table_id)
  div = document.getElementById(div_id)
  h1 = create_h1()
  h1.innerText = capitalize(table_id)

  div.appendChild(h1)
  div.appendChild(output["table"])

  
  let data_table = new DataTable("#" + table_id, {
    data: table_data,
    columns: output["columns"]
  });
}

for(key in sample_data){
  populate_table(key)
}

// populate_table("spend")