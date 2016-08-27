/* SALDO USUARIOS */
select	u.usuario_id,
			u.usuario_nome,
			sum(l.lancamento_valor) as saldo,
			(
				select 	10 - ifnull(sum(e.emprestimo_valor), 0) 
				from		emprestimo e
				where		e.emprestimo_pago = 0
							and u.usuario_id = e.usuario_id
			) as limite_emprestimo
from		usuario u
			inner join lancamento l on u.usuario_id = l.usuario_id
where		u.usuario_nome not like '%VIRTUAL%'
group by u.usuario_id
order by sum(l.lancamento_valor) desc
