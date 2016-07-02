select	p.partida_id,
			t1.time_nome,
			t2.time_nome,
			p.partida_valor,
			p.partida_valor * p.partida_fator_inicial as inicial,
			count(*) as apostas,
			format((sum(p.partida_valor) +  (p.partida_valor * p.partida_fator_inicial)) -
			((sum(p.partida_valor) +  (p.partida_valor * p.partida_fator_inicial)) * 0.3), 2) as premio,
			p.partida_realizada,
			p.partida_vencedores
from		aposta a
			inner join partida p on a.partida_id = p.partida_id
			inner join time t1 on p.time_id_mandante = t1.time_id
			inner join time t2 on p.time_id_visitante = t2.time_id
where		p.partida_serie = 1
			and p.partida_rodada = 13
group by p.partida_id
order by (sum(p.partida_valor) +  (p.partida_valor * p.partida_fator_inicial)) -
			((sum(p.partida_valor) +  (p.partida_valor * p.partida_fator_inicial)) * 0.3) desc