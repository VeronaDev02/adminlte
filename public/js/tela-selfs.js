function saveTelaPreferences(preferences) {
    axios.post('/user/save-tela-preferences', {
        preferences: preferences
    })
    .then(function (response) {
        if (response.data.success) {
            console.log('Preferências salvas com sucesso!');
        } else {
            console.error('Erro ao salvar preferências');
        }
    })
    .catch(function (error) {
        console.error('Erro ao fazer requisição AJAX para salvar preferências:', error.message);
    });
}
function destroyTelaPreferences(index) {
    axios.post(`/user/destroy-tela-preferences/${index}`)
    .then(function (response) {
        if (response.data.success) {
            console.log('Preferência removida com sucesso!');
        } else {
            console.error('Erro ao remover preferências');
        }
    })
    .catch(function (error) {
        console.error('Erro ao fazer requisição AJAX para remover preferências:', error.message);
    });
}

