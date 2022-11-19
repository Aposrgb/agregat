update products set categories_id = 27 where id > 7500 and id < 7800;
update products set categories_id = 28 where id > 7300 and id < 7500;
update products set sub_categories_id = 21 where  id < 9000;
alter sequence products_id_seq restart with 10781;
alter sequence sub_categories_id_seq restart with 31;
alter sequence categories_id_seq restart with 29;
alter sequence news_id_seq restart with 6;