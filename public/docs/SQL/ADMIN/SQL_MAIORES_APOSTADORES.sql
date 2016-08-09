/* MAIORES APOSTADORES */
select	u.usuario_nome,
			u.usuario_email,
			count(a.aposta_id) as apostas
from		aposta a
			inner join usuario u on a.usuario_id = u.usuario_id
group by a.usuario_id
order by count(a.aposta_id) desc