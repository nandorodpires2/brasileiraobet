select	u.usuario_nome,
			u.usuario_email,
			a.aposta_vencedora_premio,
			a.aposta_vencedora_valor
from		aposta a
			inner join usuario u on a.usuario_id = u.usuario_id
where		a.partida_id = 55
			and a.aposta_vencedora = 1
