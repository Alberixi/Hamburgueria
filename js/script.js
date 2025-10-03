document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Tem certeza que deseja excluir este item?')) {
                e.preventDefault();
            }
        });
    });

    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    const formatCurrency = (input) => {
        let value = input.value.replace(/\D/g, '');
        value = (value / 100).toFixed(2);
        input.value = value.replace('.', ',');
    };

    const currencyInputs = document.querySelectorAll('.currency-input');
    currencyInputs.forEach(input => {
        input.addEventListener('blur', function() {
            formatCurrency(this);
        });
    });

    const calcularTotal = () => {
        let total = 0;
        document.querySelectorAll('.item-subtotal').forEach(item => {
            const value = parseFloat(item.textContent.replace('R$ ', '').replace('.', '').replace(',', '.'));
            if (!isNaN(value)) {
                total += value;
            }
        });

        const totalElement = document.getElementById('total-pedido');
        if (totalElement) {
            totalElement.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
        }
    };

    const qtdInputs = document.querySelectorAll('.quantidade-input');
    qtdInputs.forEach(input => {
        input.addEventListener('change', function() {
            const row = this.closest('tr');
            const preco = parseFloat(row.dataset.preco);
            const qtd = parseInt(this.value) || 0;
            const subtotal = preco * qtd;

            const subtotalCell = row.querySelector('.item-subtotal');
            if (subtotalCell) {
                subtotalCell.textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
            }

            calcularTotal();
        });
    });

    if (qtdInputs.length > 0) {
        calcularTotal();
    }
});

function adicionarProduto(produtoId, produtoNome, produtoPreco) {
    const tbody = document.getElementById('produtos-pedido');
    if (!tbody) return;

    const existingRow = tbody.querySelector(`tr[data-produto-id="${produtoId}"]`);

    if (existingRow) {
        const qtdInput = existingRow.querySelector('.quantidade-input');
        qtdInput.value = parseInt(qtdInput.value) + 1;
        qtdInput.dispatchEvent(new Event('change'));
        return;
    }

    const row = document.createElement('tr');
    row.dataset.produtoId = produtoId;
    row.dataset.preco = produtoPreco;

    row.innerHTML = `
        <td>${produtoNome}</td>
        <td>R$ ${parseFloat(produtoPreco).toFixed(2).replace('.', ',')}</td>
        <td>
            <input type="number" name="produtos[${produtoId}][quantidade]" class="form-control quantidade-input" value="1" min="1" style="width: 80px;">
            <input type="hidden" name="produtos[${produtoId}][preco]" value="${produtoPreco}">
        </td>
        <td class="item-subtotal">R$ ${parseFloat(produtoPreco).toFixed(2).replace('.', ',')}</td>
        <td>
            <button type="button" class="btn btn-danger btn-small" onclick="removerProduto(this)">Remover</button>
        </td>
    `;

    tbody.appendChild(row);

    const qtdInput = row.querySelector('.quantidade-input');
    qtdInput.addEventListener('change', function() {
        const preco = parseFloat(row.dataset.preco);
        const qtd = parseInt(this.value) || 0;
        const subtotal = preco * qtd;

        const subtotalCell = row.querySelector('.item-subtotal');
        subtotalCell.textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');

        calcularTotal();
    });

    calcularTotal();
}

function removerProduto(button) {
    button.closest('tr').remove();
    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    document.querySelectorAll('.item-subtotal').forEach(item => {
        const value = parseFloat(item.textContent.replace('R$ ', '').replace('.', '').replace(',', '.'));
        if (!isNaN(value)) {
            total += value;
        }
    });

    const totalElement = document.getElementById('total-pedido');
    if (totalElement) {
        totalElement.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
    }

    const totalHidden = document.getElementById('total-hidden');
    if (totalHidden) {
        totalHidden.value = total.toFixed(2);
    }
}
