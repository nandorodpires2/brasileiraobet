/* APOSTAS USUARIOS */
select	u.usuario_nome,
			count(*) as apostas
from		aposta a
			inner join usuario u on a.usuario_id = u.usuario_id			
group by u.usuario_id
order by count(*) desc