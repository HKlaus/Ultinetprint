function change_prio() {		
	if (print_file.priority.value == 0) { print_file.numerisch.value = "keine"; }
	else if (print_file.priority.value == 1) { print_file.numerisch.value = "niedrig"; }
	else if (print_file.priority.value == 2) { print_file.numerisch.value = "normal"; }
	else if (print_file.priority.value == 3) { print_file.numerisch.value = "erhöht"; }
	else if (print_file.priority.value == 4) { print_file.numerisch.value = "höchste"; }
}