select	u.usuario_nome,
			sum(e.emprestimo_valor) as emprestimo
from		emprestimo	e
			inner join usuario u on e.usuario_id = u.usuario_id
where		e.emprestimo_pago = 0
group by u.usuario_id