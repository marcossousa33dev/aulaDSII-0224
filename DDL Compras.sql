#Criação do banco de dados, denominamos compras
create database compras;

#Habilitamos a utilização do mesmo
use compras;

#Estrutura da tabela de usuários
create table usuarios(
	id_usuario integer not null auto_increment primary key,
    usuario    varchar(15),
    senha      varchar(32),
    dtcria     datetime default now(),
    estatus    char(01) default ''
);

#Vamos inserir um usuário padrão do sistema
insert into usuarios(usuario, senha)
values('admin', md5('admin123'));

#Ao final fazemos um Select para verificar o registro lançado
select * from usuarios where senha = md5('admin123');

# Mudando a estrutura da tabela de usuário
alter table usuarios drop column id_usuario;
alter table usuarios modify usuario varchar(15) not null primary key;

#Estrutura da tabela de unidade de medida
create table unid_medida (
	cod_unidade integer auto_increment primary key,
    sigla       varchar(03) default '',
    descricao   varchar(30) default '',
    dtcria      datetime default now(),
    usucria     varchar(15),
    estatus     char(01) default '',
    
    constraint foreign key fk_unidmed_prod (usucria) references usuarios(usuario)
);

#Estrutura da tabela de produtos
create table produtos (
	cod_produto   integer auto_increment primary key,
    descricao     varchar(30) default '',
    unid_medida   integer default 0,
    estoq_mininmo integer default 0,
    estoq_maximo  integer default 0,
    dtcria        datetime default now(),
    usucria       varchar(15),
    estatus       char(01) default '',
    
    constraint foreign key fk_prod_unidmed (unid_medida) references unid_medida(cod_unidade),
    constraint foreign key fk_prod_usuarios (usucria) references usuarios(usuario)
);
