select	t1.time_nome as mandante,
			p.partida_placar_mandante as '',
			t2.time_nome as visitante,
			p.partida_placar_visitante as '',
			(
				select	count(*) 
				from		aposta a
				where		p.partida_id = a.partida_id
			) as apostas,
			p.partida_vencedores as vencedores,
			p.partida_montante as premio
from		partida p
			inner join time t1 on p.time_id_mandante = t1.time_id
			inner join time t2 on p.time_id_visitante = t2.time_id
where		p.partida_rodada = 12
			and p.partida_realizada = 1
			and p.partida_vencedores > 0
			