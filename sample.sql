



spool sample.log
set serveroutput on size 1000000;
declare

  l_sql varchar2(32000);
  l_cols varchar2(32000);
  l_vals varchar2(32000);
  l_cnt pls_integer;

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

    l_sql := 'INSERT INTO ' || i_tables.object_name || ' ( ';
    l_cols := null;
    l_vals := null;

    l_cnt := 0;
    for i_columns in c_columns( i_tables.object_name )
    loop
      l_cnt := l_cnt + 1;
      l_number_val := l_number_val + 1;

      -- ��L�[�̃J�������𒲂ׂ�B
      
      -- ��L�[�̃J��������MAX�l�{�P���擾
      

      --
      -- ��L�[�̃J�����̒l�͏�L�Ŏ擾�����l���Z�b�g����
      --      
      if ( l_cnt = 1 )
      then
        l_cols := l_cols || i_columns.column_name;

        if ( i_columns.data_type = 'NUMBER' )
        then
          l_vals := l_vals || l_number_val;
        elsif ( i_columns.data_type = 'DATE' )
        then
          l_vals := l_vals || SYSDATE;
        else
          l_vals := l_vals || to_char(l_number_val);
        end if;
      else
        l_cols := l_cols || ',' ||  i_columns.column_name;    

        if ( i_columns.data_type = 'NUMBER' )
        then
          l_vals := l_vals || ',' || l_number_val;
        elsif ( i_columns.data_type = 'DATE' )
        then
          l_vals := l_vals || ',' || SYSDATE;
        else
          l_vals := l_vals || ',' || to_char(l_number_val);
        end if;
      end if;
    end loop;

    -- INSERT ���s
  	l_sql := l_sql || l_cols || ' ) VALUES ( ' || l_vals || ' ) ';
  	execute immediate l_sql;

    -- DELETE ���s
    l_sql := 'DELETE FROM ' || i_tables.object_name || ' WHERE ' || ��L�[�̃J������ || ' = ' || l_key;
  	execute immediate l_sql;

    --
    -- �o�b�N�A�b�v�e�[�u����SELECT�`�F�b�N
    -- �ŏ���INSERT�����l�ƈقȂ�ꍇ��NG
    --

  end loop
exception
  when others
  then
    dbms_output.put_line(sqlerrm);
end;
/
show errors;
spool off;
