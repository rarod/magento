# magento
module Rarod_Sellers

Projeto criado a partir de uma instalação default do Magento. O intuito era realmente criar um ambiente ja configurado para maior facilidade nos testes, uma vez que utilizei algumas funcões padrões do Magento, dentre as principais, o Multisource.

Ao baixar o projeto, perceberá apenas um módulo em app/code. Esse que deve estar ativo (Rarod_Sellers).

O módulo foi criado utilizando como base a própria documentação do Magento.

Ao setar a loja em ambiente local, perceberá que já existem 5 produtos que foram criados rapidamente para testes.

Foram criados stores (Website, Store, Store View), Sources e tambem Stocks, onde os nomes aos sellers.

ENFIM, a idéia do módulo é usar o multisource e fazer o controle de estoque de cada Seller/Vendedor com o auxilio dele, onde em cada produto já é possível definir a quantidade por Seller.

Nesse contexto foi criado o módulo "Rarod_Sellers" (http://magento.localhost/admin/sellers), que é composto de um CRUD para poder manipular os dados via admin e tambem uma grid que traz os dados dessa tabela ('rarod_sellers').
Também foi desenvolvida uma query em graphQl que retorna os dados salvos na tabela do Seller por ID.

Endpoint: http://magento.localhost/graphql

{
        productSeller(id: "2") {
        name
        website_id
        phone
    }
}

Response:
{
        "data": {
        "productSeller": {
        "name": "vendedor1",
        "website_id": "vendedor1",
        "phone": "222222222"
        }
    }
}

Abaixo o link do loom onde mostro o cenário citado acima.

https://www.loom.com/share/54e89a82b75647d5b0f263d69fb89cae



O dump do banco usado tambem está ai.


Qualquer duvida, fico a disposição.


