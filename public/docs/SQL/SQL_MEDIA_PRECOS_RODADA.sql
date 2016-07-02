select	p.partida_rodada,
			count(*) as partidas,
			sum(p.partida_valor) as montante,
			sum(p.partida_valor) / count(*) as media,
			format(20 / (sum(p.partida_valor) / count(*)), 0) as apostas
from		partida p
where		p.partida_serie = 1
group by p.partida_rodada
having 	count(*) = 10
order by p.partida_rodada desc