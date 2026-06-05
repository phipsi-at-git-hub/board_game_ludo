// Adding event listener to define toggle groups
document.querySelectorAll('[data-toggle-group]').forEach(group => {
    const switch_element = group.querySelector('[data-toggle-switch]'); 
    const children = group.querySelectorAll('[data-toggle-target]'); 

    if (!switch_element) return; 

    const update = () => {
        children.forEach(element => {
            element.classList.toggle('active', switch_element.checked); 
        }); 
    }; 

    switch_element.addEventListener('change', update); 

    // Initial state
    update(); 
}); 
