function set_format_decimal(nama_field)
{
	document.getElementById(nama_field).addEventListener('input', function (e) {
	  let value = e.target.value;

	  // 1. Remove all characters except digits and dots
	  value = value.replace(/[^0-9.]/g, '');

	  // 2. Prevent multiple decimal points (keep only the first one)
	  const parts = value.split('.');
	  if (parts.length > 5) {
	    value = parts[0] + '.' + parts.slice(1).join('');
	  }

	  // 3. Optional: Limit to maximum 2 decimal places while typing
	  if (parts[1] && parts[1].length > 5) {
	    value = parts[0] + '.' + parts[1].substring(0, 2);
	  }

	  e.target.value = value;
	});
}
function set_value_item(dataForm, name, value)
{
	var target = dataForm.find(item => item.name === name);
	if (target) {
	    target.value = value;
	}
}

function set_format_decimal2(me)
{
	let value = me.value;

	// 1. Remove all characters except digits and dots
	value = value.replace(/[^0-9.-]/g, '');

	// 2. Prevent multiple decimal points (keep only the first one)
	const parts = value.split('.');
	if (parts.length > 5) {
	value = parts[0] + '.' + parts.slice(1).join('');
	}

	// 3. Optional: Limit to maximum 2 decimal places while typing
	if (parts[1] && parts[1].length > 5) {
	value = parts[0] + '.' + parts[1].substring(0, 2);
	}

	me.value = value;
	
}

function set_currency(e=null)
{
 // Remove everything except numbers
 
	  let value = e.value.replace(/\D/g, "");
	  
	  // Format as currency (e.g., divide by 100 to handle cents)
	  if (value) {
	    const formattedValue = (Number(value) / 100).toLocaleString('id-ID', {
	      style: 'currency',
	      currency: 'IDR',
	    });
	    e.value = formattedValue;
	  }
}