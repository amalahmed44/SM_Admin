document.addEventListener("DOMContentLoaded", function() {
    // Product Categories Chart (Using data passed from PHP)
    var productCategoriesData = '<?php echo json_encode($product_categories); ?>'
    
    // Prepare data for the chart
    var categoryLabels = productCategoriesData.map(function(category) {
        return category.category_name;
    });
    
    var categorySales = productCategoriesData.map(function(category) {
        return category.sold;
    });

    // Get the context of the canvas element for the chart
    var ctx = document.getElementById('categoriesChart').getContext('2d');
    
    var categoriesChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: categoryLabels,  // Category names as labels
            datasets: [{
                label: 'Product Categories Sold This Month',
                data: categorySales,  // Total sold quantities per category
                backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'], // Custom colors for each category
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
});
