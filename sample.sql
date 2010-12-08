spool sample.log
set serveroutput on size 1000000;
declare


cursor c_tables
is
select  *
from  user_objects
where object_type = 'TABLE';

cursor c_columns (
  v_table_name  user_tab_columns.table_name%type
)
is
select *
from user_tab_columns
where table_name = v_table_name;

begin
  for i_table in c_tables
  loop
    
  end loop
exception
  when others
  then
    dbms_output.put_line(sqlerrm);
end;
/
show errors;
spool off;
